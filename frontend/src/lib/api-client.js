export class ApiError extends Error {
  status;
  code;
  fields;
  requestId;
  constructor(status, code, message, fields = {}, requestId) {
    super(message);
    this.name = "ApiError";
    this.status = status;
    this.code = code;
    this.fields = fields;
    this.requestId = requestId;
  }
}
export function readXsrfCookie(cookies) {
  const value = cookies
    .split(";")
    .map((part) => part.trim())
    .find((part) => part.startsWith("XSRF-TOKEN="))
    ?.slice("XSRF-TOKEN=".length);
  if (!value) return undefined;
  try {
    return decodeURIComponent(value);
  } catch {
    return undefined;
  }
}
export function createApiClient(
  fetcher = fetch,
  cookieReader = () => (typeof document === "undefined" ? "" : document.cookie),
  { readTimeoutMs = 15000 } = {},
) {
  // Each mutation obtains a current CSRF cookie. No automatic retry of mutations:
  // an ambiguous network error must never submit a cart addition/order twice.
  async function csrf() {
    const response = await fetcher("/sanctum/csrf-cookie", {
      credentials: "same-origin",
      cache: "no-store",
      headers: { Accept: "application/json" },
    });
    if (!response.ok)
      throw new ApiError(
        response.status,
        "CSRF_UNAVAILABLE",
        "Không thể xác thực phiên. Vui lòng thử lại.",
      );
    const token = readXsrfCookie(cookieReader());
    if (!token)
      throw new ApiError(
        419,
        "CSRF_COOKIE_MISSING",
        "Phiên đăng nhập chưa sẵn sàng. Vui lòng tải lại trang.",
      );
    return token;
  }
  return async function request(path, options = {}) {
    const resource = path.split("?", 1)[0];
    if (
      !/^\/[a-zA-Z0-9][a-zA-Z0-9/_?=&%+~.:-]*$/.test(path) ||
      resource.includes("..") ||
      /%2e|%2f|%5c/i.test(resource)
    ) {
      throw new Error("API path must be a relative resource path.");
    }
    const method = options.method ?? "GET";
    const headers = new Headers({ Accept: "application/json" });
    if (method !== "GET") headers.set("X-XSRF-TOKEN", await csrf());
    if (options.idempotencyKey)
      headers.set("Idempotency-Key", options.idempotencyKey);
    const multipart = options.body instanceof FormData;
    if (options.body !== undefined && !multipart)
      headers.set("Content-Type", "application/json");
    // A stalled auth/catalog read must not hold the whole storefront in loading.
    // Mutations keep their existing semantics: never repeat an uncertain write.
    const read = method === "GET" ? new AbortController() : null;
    let timedOut = false;
    const abortRead = () => read.abort(options.signal.reason);
    if (read && options.signal) {
      if (options.signal.aborted) abortRead();
      else options.signal.addEventListener("abort", abortRead, { once: true });
    }
    const deadline = read
      ? setTimeout(() => {
          timedOut = true;
          read.abort();
        }, readTimeoutMs)
      : undefined;
    try {
      const response = await fetcher(`/api/v1${path}`, {
        method,
        headers,
        credentials:
          resource === "/storefront" && method === "GET"
            ? "omit"
            : "same-origin",
        cache:
          resource === "/storefront" && method === "GET"
            ? "default"
            : "no-store",
        signal: read?.signal ?? options.signal,
        body:
          options.body === undefined
            ? undefined
            : multipart
              ? options.body
              : JSON.stringify(options.body),
      });
      if (response.status === 204)
        return {
          data: undefined,
          meta: {
            request_id: response.headers.get("X-Request-ID") ?? "",
            server_time: "",
          },
        };
      const body = await response.json().catch((error) => {
        if (read?.signal.aborted || options.signal?.aborted) throw error;
        return null;
      });
      if (!response.ok)
        throw new ApiError(
          response.status,
          body?.error?.code ?? "REQUEST_FAILED",
          body?.error?.message ??
            "Không thể kết nối dịch vụ. Vui lòng thử lại.",
          body?.error?.fields ?? {},
          body?.meta?.request_id,
        );
      if (!body || !("data" in body) || !body.meta)
        throw new ApiError(
          502,
          "INVALID_RESPONSE",
          "Dịch vụ trả về dữ liệu không hợp lệ.",
        );
      return body;
    } catch (error) {
      if (timedOut && !options.signal?.aborted)
        throw new ApiError(
          504,
          "REQUEST_TIMEOUT",
          "Dịch vụ phản hồi quá lâu. Vui lòng thử lại.",
        );
      throw error;
    } finally {
      clearTimeout(deadline);
      if (read && options.signal)
        options.signal.removeEventListener("abort", abortRead);
    }
  };
}
export const api = createApiClient();
