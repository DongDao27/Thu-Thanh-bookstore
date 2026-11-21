"use client";
import Link from "next/link";
import { useRouter, useSearchParams } from "next/navigation";
import { Suspense, useEffect, useRef, useState } from "react";
import { AccountGate } from "@/components/account-gate";
import { useStore } from "@/components/store-provider";
import { Empty, Loading, Notice } from "@/components/ui";
import { api, ApiError } from "@/lib/api-client";
import { errorMessage, money } from "@/lib/format";
export default function CheckoutPage() {
  return (
    <AccountGate>
      <Suspense fallback={<Loading />}>
        <Checkout />
      </Suspense>
    </AccountGate>
  );
}
function Checkout() {
  const { user, cart, refreshCart } = useStore();
  const params = useSearchParams();
  const direct = params.has("product_id");
  const productId = Number(params.get("product_id"));
  const quantity = Number(params.get("quantity") ?? 1);
  const validSelection =
    !direct ||
    (Number.isSafeInteger(productId) &&
      productId > 0 &&
      Number.isInteger(quantity) &&
      quantity >= 1 &&
      quantity <= 99);
  const [selected, setSelected] = useState(null);
  const [selectionError, setSelectionError] = useState("");
  const [draft, setDraft] = useState(null);
  const draftKey = `thu-thanh:checkout:${user.id}`;
  const router = useRouter();
  const [method, setMethod] = useState("delivery");
  const [quote, setQuote] = useState(null);
  const [offset, setOffset] = useState(0);
  const [now, setNow] = useState(Date.now());
  const [error, setError] = useState("");
  const [fields, setFields] = useState({});
  const [busy, setBusy] = useState(false);
  const key = useRef("");
  const lock = useRef(false);
  useEffect(() => {
    let saved = {};
    try {
      const value = JSON.parse(sessionStorage.getItem(draftKey) || "null");
      if (value && Date.now() - value.savedAt < 30 * 60 * 1000) saved = value;
      else sessionStorage.removeItem(draftKey);
    } catch {}
    setDraft(saved);
    if (saved.method === "pickup") setMethod("pickup");
  }, [draftKey]);
  useEffect(() => {
    if (!direct || !validSelection) return;
    const abort = new AbortController();
    setSelected(null);
    setSelectionError("");
    setQuote(null);
    api(`/products/${productId}`, { signal: abort.signal })
      .then(({ data }) => {
        if (!abort.signal.aborted) setSelected(data);
      })
      .catch((e) => {
        if (!abort.signal.aborted) setSelectionError(errorMessage(e));
      });
    return () => abort.abort();
  }, [direct, validSelection, productId, quantity]);
  function saveDraft(form) {
    const values = Object.fromEntries(new FormData(form));
    const next = { ...draft, savedAt: Date.now() };
    for (const name of [
      "method",
      "recipient_name",
      "recipient_phone",
      "province",
      "locality",
      "address_line",
    ]) {
      if (typeof values[name] === "string") next[name] = values[name];
    }
    setDraft(next);
    try {
      sessionStorage.setItem(draftKey, JSON.stringify(next));
    } catch {}
  }
  useEffect(() => {
    const timer = setInterval(() => setNow(Date.now()), 1000);
    return () => clearInterval(timer);
  }, []);
  const remaining = quote
    ? Math.max(
        0,
        Math.ceil((Date.parse(quote.expires_at) - now - offset) / 1000),
      )
    : 0;
  async function getQuote(event) {
    event.preventDefault();
    if (lock.current) return;
    lock.current = true;
    setBusy(true);
    setError("");
    setFields({});
    const data = Object.fromEntries(new FormData(event.currentTarget));
    const input = {
      fulfillment_method: method,
      recipient_name: String(data.recipient_name),
      recipient_phone: String(data.recipient_phone),
      ...(direct ? { buy_now: { product_id: productId, quantity } } : {}),
    };
    if (method === "delivery")
      input.address = {
        address_line: String(data.address_line),
        locality: String(data.locality),
        province: String(data.province),
      };
    try {
      const result = await api("/checkout/quotes", {
        method: "POST",
        body: input,
      });
      setOffset(Date.parse(result.meta.server_time) - Date.now());
      setQuote(result.data);
      key.current = crypto.randomUUID();
      setNow(Date.now());
    } catch (error) {
      setError(errorMessage(error));
      if (error instanceof ApiError) setFields(error.fields);
    } finally {
      setBusy(false);
      lock.current = false;
    }
  }
  async function createOrder() {
    if (lock.current || !quote || !remaining) return;
    lock.current = true;
    setBusy(true);
    setError("");
    try {
      const result = await api("/orders", {
        method: "POST",
        body: { quote_id: quote.id },
        idempotencyKey: key.current,
      });
      await refreshCart().catch(() => {});
      try {
        sessionStorage.removeItem(draftKey);
      } catch {}
      const code = encodeURIComponent(result.data.order_code);
      if (method === "delivery") {
        try {
          const attempt = await api(`/orders/${code}/payment-attempts`, {
            method: "POST",
            idempotencyKey: crypto.randomUUID(),
          });
          const url = attempt.data.payment_url;
          if (url && new URL(url).protocol === "https:") {
            window.location.assign(url);
            return;
          }
        } catch {}
        router.push(`/orders/${code}?payment=unavailable`);
        return;
      }
      router.push(`/orders/${code}`);
    } catch (error) {
      setError(errorMessage(error));
      if (error instanceof ApiError && error.status === 409) setQuote(null);
    } finally {
      setBusy(false);
      lock.current = false;
    }
  }
  if (!draft || (!direct && !cart)) return <Loading />;
  if (!validSelection || selectionError)
    return (
      <div className="container page-section">
        <Notice error>
          {selectionError || "Sản phẩm hoặc số lượng không hợp lệ."}
        </Notice>
        <Link href="/products">Chọn lại sản phẩm</Link>
      </div>
    );
  if (direct && !selected) return <Loading />;
  if (direct && (!selected.is_active || selected.available_quantity < quantity))
    return (
      <div className="container page-section">
        <Notice error>
          Sản phẩm hiện không đủ số lượng bạn chọn. Vui lòng kiểm tra lại.
        </Notice>
        <Link href={`/products/${productId}`}>Điều chỉnh số lượng</Link>
      </div>
    );
  if (!direct && !cart.items.length)
    return (
      <div className="container page-section">
        <Empty title="Giỏ hàng còn trống">
          <Link href="/products" className="button primary">
            Khám phá sản phẩm
          </Link>
        </Empty>
      </div>
    );
  return (
    <div className="container page-section">
      <div className="breadcrumb">
        <Link href="/cart">Giỏ hàng</Link>
        <span>/</span>Xác nhận đơn hàng
      </div>
      <h1>Thông tin nhận hàng</h1>
      {direct && (
        <Notice>
          Mua riêng: {selected.name} × {quantity}. Các sản phẩm khác trong giỏ
          được giữ nguyên.
        </Notice>
      )}
      <Notice>
        Đơn giao hàng thanh toán qua VNPAY. Nhận tại cửa hàng có thể thanh toán
        ngay hoặc tại quầy.
      </Notice>
      {error && (
        <Notice error>
          {error}
          {Object.entries(fields).map(([field, messages]) => (
            <p key={field}>{messages.join(" ")}</p>
          ))}
        </Notice>
      )}
      <div className="checkout-layout">
        <form
          className="checkout-form form-stack"
          onSubmit={getQuote}
          onChange={(event) => {
            saveDraft(event.currentTarget);
            if (!busy) setQuote(null);
          }}
        >
          <fieldset disabled={busy}>
            <legend>Cách nhận hàng</legend>
            <div className="delivery-options">
              <label className={method === "delivery" ? "selected" : ""}>
                <input
                  type="radio"
                  name="method"
                  value="delivery"
                  checked={method === "delivery"}
                  onChange={() => setMethod("delivery")}
                />
                <span>
                  Giao tận nhà<small>Phí giao mặc định 25.000đ</small>
                </span>
              </label>
              <label className={method === "pickup" ? "selected" : ""}>
                <input
                  type="radio"
                  name="method"
                  value="pickup"
                  checked={method === "pickup"}
                  onChange={() => setMethod("pickup")}
                />
                <span>
                  Nhận tại cửa hàng
                  <small>
                    295 Tôn Đức Thắng, Hòa Khánh, Đà Nẵng · Giữ đơn 24 giờ
                  </small>
                </span>
              </label>
            </div>
            <label>
              Họ và tên
              <input
                name="recipient_name"
                autoComplete="name"
                defaultValue={draft.recipient_name ?? user?.name}
                required
                maxLength={120}
              />
            </label>
            <label>
              Số điện thoại
              <input
                name="recipient_phone"
                type="tel"
                autoComplete="tel"
                defaultValue={draft.recipient_phone ?? user?.phone ?? ""}
                required
                maxLength={20}
              />
            </label>
            {method === "delivery" && (
              <>
                <label>
                  Tỉnh / thành phố
                  <input
                    name="province"
                    defaultValue={draft.province ?? ""}
                    autoComplete="address-level1"
                    required
                    maxLength={120}
                  />
                </label>
                <label>
                  Phường / xã, địa phương
                  <input
                    name="locality"
                    defaultValue={draft.locality ?? ""}
                    autoComplete="address-level2"
                    required
                    maxLength={120}
                  />
                </label>
                <label>
                  Địa chỉ cụ thể
                  <input
                    name="address_line"
                    defaultValue={draft.address_line ?? ""}
                    autoComplete="street-address"
                    required
                    maxLength={255}
                  />
                </label>
              </>
            )}
            <button className="button primary" disabled={busy}>
              {busy ? "Đang kiểm tra…" : "Kiểm tra và lấy báo giá"}
            </button>
          </fieldset>
        </form>
        <aside className="order-summary">
          <h2>Đơn hàng của bạn</h2>
          {quote ? (
            <>
              <div className="quote-items">
                {quote.items.map((item) => (
                  <div key={item.product_id}>
                    <span>
                      {item.name} × {item.quantity}
                    </span>
                    <strong>{money(item.line_total)}</strong>
                  </div>
                ))}
              </div>
              <div className="summary-row">
                <span>Tiền hàng</span>
                <strong>{money(quote.totals.items_subtotal)}</strong>
              </div>
              <div className="summary-row">
                <span>Giảm giá</span>
                <strong>−{money(quote.totals.discount_total)}</strong>
              </div>
              <div className="summary-row">
                <span>Phí giao</span>
                <strong>{money(quote.totals.shipping_fee)}</strong>
              </div>
              <div className="summary-total">
                <span>Tổng thanh toán</span>
                <strong>{money(quote.totals.payable_total)}</strong>
              </div>
              <p className={remaining ? "muted" : "error-text"}>
                {remaining
                  ? `Báo giá còn ${Math.floor(remaining / 60)}:${String(remaining % 60).padStart(2, "0")}`
                  : "Báo giá đã hết hạn. Vui lòng lấy báo giá mới."}
              </p>
              <button
                className="button primary full"
                onClick={createOrder}
                disabled={busy || !remaining}
              >
                {busy
                  ? "Đang xử lý…"
                  : method === "delivery"
                    ? "Đặt hàng và thanh toán"
                    : "Xác nhận đặt hàng"}
              </button>
              <p className="summary-footnote">
                Không hỗ trợ đổi trả / hoàn tiền. Đặt hàng chưa đồng nghĩa đã
                thanh toán.
              </p>
            </>
          ) : (
            <p className="muted">
              Điền thông tin và kiểm tra báo giá. Tổng tiền được xác nhận bởi
              cửa hàng trước khi bạn tạo đơn.
            </p>
          )}
        </aside>
      </div>
    </div>
  );
}
