import { it, expect, vi, beforeEach } from "vitest";
import { render, screen, fireEvent } from "@testing-library/react";
import { IntentLink } from "@/components/intent-link";
const { prefetch } = vi.hoisted(() => ({ prefetch: vi.fn() }));
vi.mock("next/navigation", () => ({ useRouter: () => ({ prefetch }) }));
vi.mock("next/link", () => ({
  default: ({ prefetch: ignored, ...props }) => <a {...props} />,
}));
beforeEach(() => {
  prefetch.mockReset();
  Object.defineProperty(navigator, "connection", {
    value: undefined,
    configurable: true,
  });
});
it("prefetches on keyboard, hover or touch intent once, and refreshes after invalidation", () => {
  const { rerender } = render(<IntentLink href="/products/1">Sách</IntentLink>);
  const link = screen.getByRole("link");
  expect(prefetch).not.toHaveBeenCalled();
  fireEvent.focus(link);
  fireEvent.mouseEnter(link);
  fireEvent.touchStart(link);
  expect(prefetch).toHaveBeenCalledTimes(1);
  expect(prefetch).toHaveBeenCalledWith(
    "/products/1",
    expect.objectContaining({ onInvalidate: expect.any(Function) }),
  );
  prefetch.mock.calls[0][1].onInvalidate();
  fireEvent.mouseEnter(link);
  expect(prefetch).toHaveBeenCalledTimes(2);
  rerender(<IntentLink href="/products/2">Sách mới</IntentLink>);
  fireEvent.touchStart(link);
  expect(prefetch).toHaveBeenLastCalledWith("/products/2", expect.any(Object));
});
it.each([
  { saveData: true },
  { effectiveType: "2g" },
  { effectiveType: "slow-2g" },
])("respects network preference %o", (connection) => {
  Object.defineProperty(navigator, "connection", {
    value: connection,
    configurable: true,
  });
  render(<IntentLink href="/products/1">Sách</IntentLink>);
  fireEvent.focus(screen.getByRole("link"));
  expect(prefetch).not.toHaveBeenCalled();
});
