import { test } from "node:test";
import assert from "node:assert/strict";
import {
  ApiError,
  createApiClient,
  readXsrfCookie,
} from "../src/lib/api-client.js";
test("anonymous snapshot omits credentials and permits HTTP caching; private home does not", async () => {
  const calls = [];
  const api = createApiClient(async (_path, init) => {
    calls.push(init);
    return Response.json({
      data: {},
      meta: { request_id: "trace", server_time: "now" },
    });
  });
  await api("/storefront");
  await api("/home");
  assert.equal(calls[0].credentials, "omit");
  assert.equal(calls[0].cache, "default");
  assert.equal(calls[1].credentials, "same-origin");
  assert.equal(calls[1].cache, "no-store");
});
test("mutation first obtains CSRF and sends decoded token, cookie and idempotency key", async () => {
  const calls = [];
  const fetcher = async (path, init) => {
    calls.push({ path: String(path), init });
    return String(path).startsWith("/sanctum")
      ? new Response(null, { status: 204 })
      : Response.json(
          {
            data: { order_code: "TT-123" },
            meta: { request_id: "id", server_time: "time" },
          },
          { status: 201 },
        );
  };
  const api = createApiClient(
    fetcher,
    () => "XSRF-TOKEN=a%2Bb%3D; other=value",
  );
  await api("/orders", {
    method: "POST",
    body: { quote_id: 1 },
    idempotencyKey: "same-order-key",
  });
  assert.deepEqual(
    calls.map((call) => call.path),
    ["/sanctum/csrf-cookie", "/api/v1/orders"],
  );
  const request = calls[1].init;
  assert.equal(request.credentials, "same-origin");
  assert.equal(new Headers(request.headers).get("X-XSRF-TOKEN"), "a+b=");
  assert.equal(
    new Headers(request.headers).get("Idempotency-Key"),
    "same-order-key",
  );
  assert.equal(request.body, '{"quote_id":1}');
});
test("CSRF rejection does not send the mutation or retry it", async () => {
  let requests = 0;
  const api = createApiClient(async () => {
    requests++;
    return new Response(null, { status: 503 });
  });
  await assert.rejects(
    api("/cart/items", {
      method: "POST",
      body: { product_id: 1, quantity: 1 },
    }),
    (error) => error instanceof ApiError && error.code === "CSRF_UNAVAILABLE",
  );
  assert.equal(requests, 1);
});
test("ambiguous network failures never automatically repeat a cart mutation", async () => {
  let mutations = 0;
  const api = createApiClient(
    async (path) => {
      if (path.startsWith("/sanctum"))
        return new Response(null, { status: 204 });
      mutations++;
      throw new TypeError("network disconnected");
    },
    () => "XSRF-TOKEN=valid",
  );
  await assert.rejects(
    api("/cart/items", {
      method: "POST",
      body: { quantity: 1, product_id: 1 },
    }),
  );
  assert.equal(mutations, 1);
});
test("validation/status/request-id survive API error responses", async () => {
  const api = createApiClient(async () =>
    Response.json(
      {
        error: {
          code: "VALIDATION_ERROR",
          message: "Invalid input",
          fields: { email: ["Invalid email"] },
        },
        meta: { request_id: "trace" },
      },
      { status: 422 },
    ),
  );
  await assert.rejects(
    api("/products"),
    (error) =>
      error instanceof ApiError &&
      error.status === 422 &&
      error.requestId === "trace" &&
      error.fields.email[0] === "Invalid email",
  );
});
test("external and traversal destinations rejected before fetch", async () => {
  const api = createApiClient(async () => {
    throw new Error("must not fetch");
  });
  for (const path of [
    "https://example.test",
    "//example.test",
    "/../admin",
    "/%2e%2e/admin",
    "/products%2F../auth",
  ]) {
    await assert.rejects(api(path), /relative resource/);
  }
  assert.equal(readXsrfCookie("XSRF-TOKEN=%malformed"), undefined);
});
test("encoded punctuation in a search query does not become a resource-path restriction", async () => {
  let requested = "";
  const api = createApiClient(async (path) => {
    requested = path;
    return Response.json({
      data: [],
      meta: { request_id: "trace", server_time: "now" },
    });
  });
  await api("/products?q=book%2Fpen..&sort=newest");
  assert.equal(requested, "/api/v1/products?q=book%2Fpen..&sort=newest");
});
