"use client";
import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useRef,
  useState,
} from "react";
import { useRouter } from "next/navigation";
import Image from "next/image";
import { api, ApiError } from "@/lib/api-client";
import { publicStorefront } from "@/lib/public-storefront";
import { ProductPrice } from "./product-price";
import { ProductDescription } from "./product-description";
import { errorMessage } from "@/lib/format";
import { imageSrc } from "@/lib/product-images";
import { Modal, Notice } from "./ui";
import Link from "next/link";
import { PasswordField } from "./password-field";
const Context = createContext(null);
export function useStore() {
  const store = useContext(Context);
  if (!store) throw new Error("StoreProvider missing");
  return store;
}
export function StoreProvider({ children }) {
  const router = useRouter();
  const [user, setUser] = useState(null);
  const [authLoading, setAuthLoading] = useState(true);
  const pendingPurchase = useRef(null);
  const [cart, setCart] = useState(null);
  const [categories, setCategories] = useState([]);
  const [brands, setBrands] = useState([]);
  const updateProfile = useCallback(async (input) => {
    const result = await api("/auth/profile", {
      method: "PATCH",
      body: input,
    });
    setUser(result.data);
    return result.data;
  }, []);
  const [showAuth, setShowAuth] = useState(false);
  const [authRegister, setAuthRegister] = useState(false);
  const requireLogin = useCallback(() => {
    pendingPurchase.current = null;
    setAuthRegister(false);
    setShowAuth(true);
  }, []);
  const requireRegistration = useCallback(() => {
    pendingPurchase.current = null;
    setAuthRegister(true);
    setShowAuth(true);
  }, []);
  const [productQuantity, setProductQuantity] = useState(1);
  const [product, setProduct] = useState(null);
  const [toast, setToast] = useState("");
  const timer = useRef(null);
  const notify = useCallback((message) => {
    setToast(message);
    if (timer.current) clearTimeout(timer.current);
    timer.current = setTimeout(() => setToast(""), 5000);
  }, []);
  const refreshCart = useCallback(async () => {
    setCart((await api("/cart")).data);
  }, []);
  const refreshCategories = useCallback(async () => {
    setCategories((await api("/categories")).data);
  }, []);
  useEffect(() => {
    const abort = new AbortController();
    publicStorefront()
      .then((result) => {
        if (!abort.signal.aborted) {
          setCategories(result.data.categories);
          setBrands(result.data.brands ?? []);
        }
      })
      .catch(() => {});
    const mayHaveSession = document.cookie
      .split(";")
      .some((cookie) => cookie.trim().startsWith("XSRF-TOKEN="));
    if (!mayHaveSession) setAuthLoading(false);
    else
      api("/auth/me", { signal: abort.signal })
        .then(async (result) => {
          setUser(result.data);
          if (result.data.role === "customer") await refreshCart();
        })
        .catch((error) => {
          if (
            !(error instanceof ApiError && error.status === 401) &&
            !abort.signal.aborted
          )
            notify(errorMessage(error));
        })
        .finally(() => {
          if (!abort.signal.aborted) setAuthLoading(false);
        });
    return () => {
      abort.abort();
      if (timer.current) clearTimeout(timer.current);
    };
  }, [notify, refreshCart]);
  function openProduct(item) {
    setProductQuantity(1);
    setProduct(item);
  }
  function requireProductLogin(
    item,
    quantity,
    destination = "cart",
    restoreProduct = true,
  ) {
    pendingPurchase.current = {
      product: item,
      quantity,
      destination,
      restoreProduct,
    };
    setProductQuantity(quantity);
    setProduct(null);
    setAuthRegister(false);
    setShowAuth(true);
  }
  async function purchaseProduct(item, quantity, destination) {
    if (!item.is_active || quantity < 1 || quantity > item.available_quantity)
      throw new Error("Sản phẩm không đủ tồn kho.");
    if (!user) return requireProductLogin(item, quantity, destination, false);
    if (user.role !== "customer")
      throw new Error("Vui lòng dùng tài khoản khách hàng để mua sắm.");
    if (destination === "checkout") {
      router.push(`/checkout?product_id=${item.id}&quantity=${quantity}`);
      return;
    }
    await api("/cart/items", {
      method: "POST",
      body: { product_id: item.id, quantity },
    });
    let refreshed = true;
    await refreshCart().catch(() => {
      refreshed = false;
    });
    notify(
      refreshed
        ? "Đã thêm sản phẩm vào giỏ hàng."
        : "Đã thêm sản phẩm. Vui lòng tải lại giỏ hàng để xem cập nhật.",
    );
  }
  async function logout() {
    await api("/auth/logout", { method: "POST" });
    setUser(null);
    setCart(null);
    router.push("/");
    notify("Đã đăng xuất.");
  }
  return (
    <Context.Provider
      value={{
        user,
        authLoading,
        cart,
        categories,
        brands,
        updateProfile,
        refreshCart,
        refreshCategories,
        requireLogin,
        requireRegistration,
        openProduct,
        requireProductLogin,
        purchaseProduct,
        notify,
        logout,
      }}
    >
      {children}
      {showAuth && (
        <AuthDialog
          initialRegister={authRegister}
          onClose={() => {
            setShowAuth(false);
            const intent = pendingPurchase.current;
            pendingPurchase.current = null;
            if (intent && intent.restoreProduct !== false)
              setProduct(intent.product);
          }}
          onSuccess={async (account) => {
            const intent = pendingPurchase.current;
            pendingPurchase.current = null;
            setUser(account);
            setShowAuth(false);
            if (account.role !== "customer") {
              setProduct(null);
              router.push("/admin");
              return;
            }
            if (!intent) {
              await refreshCart().catch(() => {});
              return;
            }
            if (intent.destination === "checkout") {
              await refreshCart().catch(() => {});
              router.push(
                `/checkout?product_id=${intent.product.id}&quantity=${intent.quantity}`,
              );
              return;
            }
            // Consume intent before the mutation: rerenders or a second auth submit cannot replay it.
            try {
              await api("/cart/items", {
                method: "POST",
                body: {
                  product_id: intent.product.id,
                  quantity: intent.quantity,
                },
              });
            } catch (error) {
              await refreshCart().catch(() => {});
              notify(
                `Đã đăng nhập. Chưa thể hoàn tất thêm sản phẩm: ${errorMessage(error)} Kiểm tra giỏ hàng trước khi thử lại.`,
              );
              router.push("/cart");
              return;
            }
            let refreshed = true;
            await refreshCart().catch(() => {
              refreshed = false;
            });
            notify(
              refreshed
                ? "Đã thêm sản phẩm vào giỏ hàng."
                : "Đã thêm sản phẩm. Vui lòng tải lại giỏ hàng để xem cập nhật.",
            );
            router.push("/cart");
          }}
        />
      )}
      {product && (
        <BuyDialog
          product={product}
          initialQuantity={productQuantity}
          onClose={() => setProduct(null)}
        />
      )}
      {toast && (
        <div className="toast" role="status">
          {toast}
          <button aria-label="Đóng thông báo" onClick={() => setToast("")}>
            ×
          </button>
        </div>
      )}
    </Context.Provider>
  );
}
function AuthDialog({ initialRegister, onClose, onSuccess }) {
  const [register, setRegister] = useState(initialRegister);
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState("");
  const [fields, setFields] = useState({});
  const lock = useRef(false);
  async function submit(event) {
    event.preventDefault();
    if (lock.current) return;
    lock.current = true;
    setBusy(true);
    setError("");
    setFields({});
    const data = Object.fromEntries(new FormData(event.currentTarget));
    let body = {
      identifier: data.identifier,
      password: data.password,
    };
    if (register)
      body = {
        name: data.name,
        ...(String(data.identifier).includes("@")
          ? { email: data.identifier }
          : { phone: data.identifier }),
        password: data.password,
        password_confirmation: data.password_confirmation,
      };
    try {
      await onSuccess(
        (
          await api(register ? "/auth/register" : "/auth/login", {
            method: "POST",
            body,
          })
        ).data,
      );
    } catch (error) {
      setError(errorMessage(error));
      if (error instanceof ApiError) setFields(error.fields);
    } finally {
      lock.current = false;
      setBusy(false);
    }
  }
  return (
    <Modal
      title={register ? "Tạo tài khoản" : "Chào bạn trở lại"}
      onClose={() => {
        if (!busy) onClose();
      }}
    >
      <p className="muted">
        {register
          ? "Cùng Thu Thanh tìm những điều bạn yêu thích."
          : "Đăng nhập để mua sắm và theo dõi đơn hàng."}
      </p>
      <form className="form-stack" onSubmit={submit}>
        {register && (
          <label>
            Họ và tên
            <input name="name" autoComplete="name" required maxLength={120} />
          </label>
        )}
        <label>
          Email hoặc số điện thoại
          <input
            name="identifier"
            autoComplete="username"
            required
            maxLength={254}
            aria-invalid={!!(fields.identifier || fields.email || fields.phone)}
            aria-describedby={
              fields.identifier || fields.email || fields.phone
                ? "auth-identifier-error"
                : undefined
            }
          />
        </label>
        {(fields.identifier || fields.email || fields.phone) && (
          <small className="field-error" id="auth-identifier-error">
            {(fields.identifier ?? fields.email ?? fields.phone).join(" ")}
          </small>
        )}
        <PasswordField
          newPassword={register}
          error={fields.password?.join(" ")}
          disabled={busy}
        />
        {register && (
          <>
            <PasswordField
              name="password_confirmation"
              label="Nhập lại mật khẩu"
              newPassword
              error={fields.password_confirmation?.join(" ")}
              disabled={busy}
            />
          </>
        )}
        {error && (
          <Notice error>
            {error}
            {Object.entries(fields).map(([name, messages]) => (
              <p key={name}>{messages.join(" ")}</p>
            ))}
          </Notice>
        )}
        <button className="button primary full" disabled={busy}>
          {busy ? "Đang xử lý…" : register ? "Đăng ký" : "Đăng nhập"}
        </button>
        {!register && (
          <Link
            className="forgot-password-link"
            href="/forgot-password"
            onClick={onClose}
          >
            Quên mật khẩu?
          </Link>
        )}
      </form>
      <p className="auth-switch">
        {register ? "Đã có tài khoản?" : "Bạn chưa có tài khoản?"}{" "}
        <button
          className="text-button"
          onClick={() => {
            setRegister(!register);
            setError("");
            setFields({});
          }}
        >
          {register ? "Đăng nhập" : "Đăng ký"}
        </button>
      </p>
    </Modal>
  );
}
function BuyDialog({ product, initialQuantity, onClose }) {
  const { refreshCart, notify, user, requireProductLogin } = useStore();
  const router = useRouter();
  const [quantity, setQuantity] = useState(initialQuantity);
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState("");
  const lock = useRef(false);
  const max = Math.min(99, product.available_quantity);
  async function add(checkout) {
    if (
      lock.current ||
      !Number.isInteger(quantity) ||
      quantity < 1 ||
      quantity > max
    )
      return;
    if (!user) {
      requireProductLogin(product, quantity, checkout ? "checkout" : "cart");
      return;
    }
    if (user.role !== "customer" || !product.is_active) return;
    if (checkout) {
      onClose();
      router.push(`/checkout?product_id=${product.id}&quantity=${quantity}`);
      return;
    }
    lock.current = true;
    setBusy(true);
    setError("");
    try {
      await api("/cart/items", {
        method: "POST",
        body: { product_id: product.id, quantity },
      });
      let refreshed = true;
      await refreshCart().catch(() => {
        refreshed = false;
      });
      onClose();
      notify(
        refreshed
          ? "Đã thêm sản phẩm vào giỏ hàng."
          : "Đã thêm sản phẩm. Vui lòng tải lại giỏ hàng để xem cập nhật.",
      );
    } catch (error) {
      setError(errorMessage(error));
    } finally {
      lock.current = false;
      setBusy(false);
    }
  }
  const available = product.is_active && max > 0;
  const invalidQuantity =
    !Number.isInteger(quantity) || quantity < 1 || quantity > max;
  const disabled =
    busy ||
    !available ||
    (user !== null && user.role !== "customer") ||
    invalidQuantity;
  const src = product.images[0] ? imageSrc(product.images[0].url) : "";
  return (
    <Modal
      title={`Thông tin sản phẩm: ${product.name}`}
      className="quick-view-modal"
      onClose={() => {
        if (!busy) onClose();
      }}
    >
      <div className="quick-view-layout">
        <div className="quick-view-image">
          {src && (
            <Image
              src={src}
              alt={product.images[0]?.alt_text ?? product.name}
              fill
              sizes="(max-width: 700px) 80vw, 420px"
              style={{ objectFit: "contain" }}
              loading="eager"
            />
          )}
        </div>
        <div className="quick-view-info">
          <h2>{product.name}</h2>
          <p className="quick-view-meta">
            {product.category.name} <span>· Mã sản phẩm: {product.sku}</span>
          </p>
          <div className="quick-view-price">
            <ProductPrice product={product} />
          </div>
          <p className="muted">
            {available
              ? `Còn ${product.available_quantity} sản phẩm`
              : "Tạm hết hàng"}
          </p>
          <label htmlFor="quick-quantity">Số lượng</label>
          <div className="quick-quantity">
            <button
              aria-label="Giảm số lượng"
              disabled={busy || quantity <= 1}
              onClick={() => setQuantity(quantity - 1)}
            >
              −
            </button>
            <input
              id="quick-quantity"
              aria-label="Số lượng"
              type="number"
              min={1}
              max={Math.max(1, max)}
              disabled={busy || !available}
              aria-invalid={available && invalidQuantity}
              aria-describedby={
                available && invalidQuantity
                  ? "quick-quantity-error"
                  : undefined
              }
              value={quantity}
              onChange={(event) => setQuantity(Number(event.target.value))}
            />
            <button
              aria-label="Tăng số lượng"
              disabled={busy || quantity >= max}
              onClick={() => setQuantity(quantity + 1)}
            >
              +
            </button>
          </div>
          {available && invalidQuantity && (
            <p id="quick-quantity-error" className="error-text" role="alert">
              Nhập số nguyên từ 1 đến {max}.
            </p>
          )}
          {user && user.role !== "customer" && (
            <p className="muted">Hãy dùng tài khoản khách hàng để đặt mua.</p>
          )}
          {error && <Notice error>{error}</Notice>}
          <div className="quick-view-actions">
            <button
              className="button"
              disabled={disabled}
              onClick={() => add(false)}
            >
              {busy ? "Đang xử lý…" : "Thêm vào giỏ"}
            </button>
            <button
              className="button primary"
              disabled={disabled}
              onClick={() => add(true)}
            >
              Mua ngay
            </button>
          </div>
          <div className="quick-view-description">
            <h3>Mô tả sản phẩm</h3>
            <ProductDescription product={product} />
            <Link
              className="detail-read-more"
              href={`/products/${product.id}`}
              onClick={onClose}
            >
              Xem đầy đủ thông tin →
            </Link>
          </div>
        </div>
      </div>
    </Modal>
  );
}
