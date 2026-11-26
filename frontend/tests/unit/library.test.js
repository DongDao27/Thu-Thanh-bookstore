import { it, expect, vi } from "vitest";
import { homeCollections } from "@/lib/home-collections";
import { displayedDiscount, previewDiscountPrice } from "@/lib/pricing";
import { errorMessage, money, statusLabel } from "@/lib/format";
import { ApiError, createApiClient, readXsrfCookie } from "@/lib/api-client";
it("preserves feed priority and deduplicates real products", () => {
  const products = Array.from({ length: 20 }, (_, id) => ({ id }));
  const result = homeCollections(
    { bestseller: products.slice(0, 10), recommended: products.slice(5, 18) },
    products,
  );
  expect(result.bestseller).toHaveLength(10);
  expect(result.new).toHaveLength(10);
  expect(result.recommended).toEqual([]);
  expect(
    homeCollections({ bestseller: [], recommended: products }, products, {
      newLimit: 1,
      recommendedLimit: 2,
    }).recommended,
  ).toHaveLength(2);
});
it("formats server errors, prices and display-only discounts", () => {
  expect(displayedDiscount({ list_price: 0, unit_price: 0 })).toBe(0);
  expect(displayedDiscount({ list_price: 100, unit_price: 120 })).toBe(0);
  expect(previewDiscountPrice(10001, 5)).toBe(9501);
  expect(money(100000)).toContain("100.000");
  expect(errorMessage(new Error("secret"))).not.toContain("secret");
  expect(errorMessage(new ApiError(401, "UNAUTHENTICATED", "server"))).toBe(
    "Bạn cần đăng nhập để tiếp tục.",
  );
  expect(statusLabel.paid).toBe("Đã thanh toán");
});
it("requests anonymous storefront without cookies and returns structured API errors", async () => {
  const fetcher = vi
    .fn()
    .mockResolvedValue(Response.json({ data: { products: [] }, meta: {} }));
  const api = createApiClient(fetcher);
  expect(await api("/storefront")).toEqual({
    data: { products: [] },
    meta: {},
  });
  expect(fetcher.mock.calls[0][1].credentials).toBe("omit");
  await expect(api("https://outside.example.test")).rejects.toThrow();
});
