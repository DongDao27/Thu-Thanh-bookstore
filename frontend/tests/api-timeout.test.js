import { test } from "node:test";
import assert from "node:assert/strict";
import { ApiError, createApiClient } from "../src/lib/api-client.js";

function pendingUntilAbort(signal) {
  return new Promise((_, reject) => {
    if (signal.aborted) reject(signal.reason);
    else
      signal.addEventListener("abort", () => reject(signal.reason), {
        once: true,
      });
  });
}
const success = () => Response.json({ data: [], meta: { request_id: "test" } });

test("hung catalog reads time out without retry and the next read can recover", async () => {
  let calls = 0;
  const api = createApiClient(
    (_path, init) => {
      calls += 1;
      return calls === 1 ? pendingUntilAbort(init.signal) : success();
    },
    () => "",
    { readTimeoutMs: 25 },
  );
  await assert.rejects(
    api("/storefront"),
    (error) =>
      error instanceof ApiError &&
      error.status === 504 &&
      error.code === "REQUEST_TIMEOUT",
  );
  assert.equal(calls, 1);
  assert.deepEqual((await api("/storefront")).data, []);
  assert.equal(calls, 2);
});

test("the read deadline also covers a response body stalled after headers", async () => {
  const api = createApiClient(
    (_path, init) => ({
      status: 200,
      ok: true,
      json: () => pendingUntilAbort(init.signal),
    }),
    () => "",
    { readTimeoutMs: 25 },
  );
  await assert.rejects(api("/products"), { code: "REQUEST_TIMEOUT" });
});

test("caller cancellation remains an abort and does not cancel another read", async () => {
  const caller = new AbortController();
  const api = createApiClient((path, init) =>
    path.endsWith("/auth/me") ? pendingUntilAbort(init.signal) : success(),
  );
  const cancelled = api("/auth/me", { signal: caller.signal });
  caller.abort();
  await assert.rejects(cancelled, { name: "AbortError" });
  assert.deepEqual((await api("/products")).data, []);
});

test("an already cancelled read preserves the caller's abort", async () => {
  const caller = new AbortController();
  caller.abort();
  const api = createApiClient((_path, init) => pendingUntilAbort(init.signal));
  await assert.rejects(api("/auth/me", { signal: caller.signal }), {
    name: "AbortError",
  });
});

test("read deadlines do not abort or repeat a slow mutation", async () => {
  let writes = 0;
  const api = createApiClient(
    async (path) => {
      if (path === "/sanctum/csrf-cookie")
        return new Response(null, { status: 204 });
      writes += 1;
      await new Promise((resolve) => setTimeout(resolve, 25));
      return success();
    },
    () => "XSRF-TOKEN=test",
    { readTimeoutMs: 5 },
  );
  assert.deepEqual(
    (await api("/orders", { method: "POST", body: {} })).data,
    [],
  );
  assert.equal(writes, 1);
});
