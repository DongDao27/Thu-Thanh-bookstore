import { describe, it, expect, vi } from "vitest";
import { render, screen, fireEvent, waitFor } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import { PasswordField } from "@/components/password-field";
import { ProductPrice } from "@/components/product-price";
import {
  ProductDescription,
  productCopy,
} from "@/components/product-description";
import { NewsletterForm } from "@/components/newsletter-form";
import { Notice, Loading, Empty } from "@/components/ui";
import { api } from "@/lib/api-client";
vi.mock("@/lib/api-client", async (importOriginal) => ({
  ...(await importOriginal()),
  api: vi.fn(),
}));
describe("PasswordField", () => {
  it("toggles visibility without changing the password", async () => {
    const user = userEvent.setup();
    render(<PasswordField />);
    const input = screen.getByLabelText("Mật khẩu", { exact: true });
    await user.type(input, "keep-this-password");
    expect(input).toHaveAttribute("type", "password");
    await user.click(screen.getByRole("button", { name: "Hiện mật khẩu" }));
    expect(input).toHaveAttribute("type", "text");
    expect(input).toHaveValue("keep-this-password");
    expect(screen.getByRole("button")).toHaveAttribute("aria-pressed", "true");
    await user.click(screen.getByRole("button"));
    expect(input).toHaveAttribute("type", "password");
  });
  it("associates new-password constraints, hint and field errors", () => {
    const { rerender } = render(<PasswordField newPassword />);
    expect(screen.getByLabelText("Mật khẩu", { exact: true })).toHaveAttribute(
      "minlength",
      "12",
    );
    expect(
      screen.getByLabelText("Mật khẩu", { exact: true }),
    ).toHaveAccessibleDescription("Từ 12 đến 128 ký tự.");
    rerender(<PasswordField newPassword error="Mật khẩu quá ngắn" disabled />);
    expect(screen.getByRole("alert")).toHaveTextContent("Mật khẩu quá ngắn");
    expect(screen.getByLabelText("Mật khẩu", { exact: true })).toBeDisabled();
    expect(screen.getByRole("button")).toBeDisabled();
  });
});
describe("ProductPrice and description", () => {
  it("shows server price, original price and discount", () => {
    render(
      <ProductPrice
        product={{
          list_price: 100000,
          unit_price: 75000,
          discount_percent: 25,
        }}
      />,
    );
    expect(screen.getByText(/75.000/)).toBeInTheDocument();
    expect(screen.getByText(/100.000/).tagName).toBe("DEL");
    expect(screen.getByText("- 25%")).toBeInTheDocument();
  });
  it("hides discount for full price and handles rounded discounts", () => {
    const { container, rerender } = render(
      <ProductPrice product={{ list_price: 100, unit_price: 100 }} />,
    );
    expect(container.querySelector("del")).toBeNull();
    rerender(
      <ProductPrice product={{ list_price: 10000, unit_price: 9999 }} />,
    );
    expect(screen.getByText("Giảm giá")).toBeInTheDocument();
  });
  it("extracts metadata and renders plain text safely", () => {
    const product = {
      name: "Sách",
      category: { name: "Văn học" },
      description:
        "Nhóm: Sách\nTác giả: An\nNXB / Thương hiệu: Kim Đồng\nĐoạn một\n\n<script>alert(1)</script>",
    };
    expect(productCopy(product)).toEqual({
      author: "An",
      publisher: "Kim Đồng",
      text: "Đoạn một\n\n<script>alert(1)</script>",
    });
    const { container, rerender } = render(
      <ProductDescription product={product} />,
    );
    expect(container.querySelector("script")).toBeNull();
    expect(container.querySelectorAll("p")).toHaveLength(2);
    rerender(
      <ProductDescription product={{ ...product, description: null }} />,
    );
    expect(
      screen.getByText("Sách thuộc danh mục văn học."),
    ).toBeInTheDocument();
  });
});
describe("NewsletterForm", () => {
  it("sends email once during a pending request and resets on success", async () => {
    let complete;
    api.mockImplementationOnce(
      () =>
        new Promise((resolve) => {
          complete = resolve;
        }),
    );
    render(<NewsletterForm />);
    const input = screen.getByLabelText("Email nhận bản tin");
    fireEvent.change(input, { target: { value: "reader@example.test" } });
    fireEvent.submit(input.closest("form"));
    fireEvent.submit(input.closest("form"));
    expect(api).toHaveBeenCalledTimes(1);
    expect(api).toHaveBeenCalledWith("/newsletter/subscriptions", {
      method: "POST",
      body: { email: "reader@example.test" },
    });
    expect(input).toBeDisabled();
    complete({ data: { message: "Đăng ký thành công" } });
    await screen.findByRole("status");
    expect(input).toHaveValue("");
    expect(input).not.toBeDisabled();
    api.mockClear();
  });
  it("shows network error and permits retry", async () => {
    api.mockRejectedValueOnce(new Error("network"));
    render(<NewsletterForm />);
    const input = screen.getByLabelText("Email nhận bản tin");
    fireEvent.change(input, { target: { value: "reader@example.test" } });
    fireEvent.submit(input.closest("form"));
    expect(await screen.findByRole("alert")).toHaveTextContent(
      "Không thể kết nối",
    );
    await waitFor(() => expect(input).not.toBeDisabled());
    expect(input).toHaveValue("reader@example.test");
    api.mockClear();
  });
});
it("announces errors, loading and empty results accessibly", () => {
  const { rerender } = render(<Notice error>Không hợp lệ</Notice>);
  expect(screen.getByRole("alert")).toHaveTextContent("Không hợp lệ");
  rerender(<Notice>Đã lưu</Notice>);
  expect(screen.getByRole("status")).toHaveTextContent("Đã lưu");
  rerender(<Loading />);
  expect(screen.getByRole("status")).toHaveTextContent("Đang tải dữ liệu");
  rerender(
    <Empty title="Chưa có sản phẩm">
      <p>Thử lại sau</p>
    </Empty>,
  );
  expect(screen.getByRole("heading")).toHaveTextContent("Chưa có sản phẩm");
});
