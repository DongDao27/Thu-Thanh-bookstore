"use client";
import Link from "next/link";
import { useCallback, useEffect, useState } from "react";
import { useOrderPolling } from "@/lib/use-order-polling";
import { useStore } from "@/components/store-provider";
import { PaymentResult } from "@/components/payment-result";
import { Loading, Notice } from "@/components/ui";
import { api } from "@/lib/api-client";
import { errorMessage } from "@/lib/format";
// Only Laravel's verified payment state can display a successful payment.
export default function VnpayReturnPage() {
  const [result, setResult] = useState(null);
  const [order, setOrder] = useState(null);
  const [error, setError] = useState("");
  const { user } = useStore();
  const updateOrder = useCallback((updated) => {
    setOrder(updated);
    setResult((current) =>
      current
        ? { ...current, payment_status: updated.payment_status }
        : current,
    );
  }, []);
  const polling = useOrderPolling(
    result?.order_code,
    user?.role === "customer" &&
      result !== null &&
      result.payment_status !== "paid" &&
      result.result !== "failed",
    updateOrder,
  );
  useEffect(() => {
    const abort = new AbortController();
    const signed = new URLSearchParams(
      [...new URLSearchParams(window.location.search)].filter(([key]) =>
        key.startsWith("vnp_"),
      ),
    );
    api(`/payments/vnpay/return?${signed}`, { signal: abort.signal })
      .then(({ data }) => {
        if (!abort.signal.aborted) setResult(data);
      })
      .catch((error) => {
        if (!abort.signal.aborted) setError(errorMessage(error));
      });
    return () => abort.abort();
  }, []);
  useEffect(() => {
    if (!result?.order_code || user?.role !== "customer") return;
    const abort = new AbortController();
    api(`/orders/${encodeURIComponent(result.order_code)}`, {
      signal: abort.signal,
    })
      .then(({ data }) => {
        if (!abort.signal.aborted) updateOrder(data);
      })
      .catch(() => {});
    return () => abort.abort();
  }, [result?.order_code, user?.role, updateOrder]);
  return (
    <div className="container page-section payment-result-page">
      <div className="breadcrumb">
        <Link href="/orders">Đơn hàng của tôi</Link>
        <span>/</span>Kết quả thanh toán
      </div>
      <h1 className="sr-only">Kết quả thanh toán VNPAY</h1>
      {result &&
        result.payment_status !== "paid" &&
        result.result !== "failed" &&
        (polling.stopped || polling.error) && (
          <Notice>
            Chưa nhận được kết quả mới từ máy chủ.{" "}
            <button
              className="text-button"
              onClick={polling.refresh}
              disabled={polling.refreshing}
            >
              Đọc lại trạng thái
            </button>{" "}
            Không thanh toán lại khi chưa rõ kết quả.
          </Notice>
        )}
      {!result && !error ? (
        <Loading />
      ) : (
        <PaymentResult result={result} order={order} error={error} />
      )}
    </div>
  );
}
