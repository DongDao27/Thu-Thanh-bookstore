"use client";
import Link from "next/link";
import { useEffect, useState } from "react";
import { api } from "@/lib/api-client";
import { errorMessage } from "@/lib/format";
import { ProductActions } from "./product-actions";
import { ProductPicture } from "./product-picture";
import { ProductPrice } from "./product-price";
import { ProductDescription, productCopy } from "./product-description";
import { Icon, Loading, Notice } from "./ui";
export function ProductDetail({ id }) {
  const [product, setProduct] = useState(null);
  const [error, setError] = useState("");
  const [retry, setRetry] = useState(0);
  const [quantity, setQuantity] = useState(1);
  const [image, setImage] = useState(0);
  useEffect(() => {
    const controller = new AbortController();
    setError("");
    setProduct(null);
    api(`/products/${encodeURIComponent(id)}`, { signal: controller.signal })
      .then(({ data }) => {
        if (!controller.signal.aborted) {
          setProduct(data);
          setImage(0);
          setQuantity(1);
        }
      })
      .catch((e) => {
        if (!controller.signal.aborted) setError(errorMessage(e));
      });
    return () => controller.abort();
  }, [id, retry]);
  if (!product)
    return (
      <div className="container page-section">
        {error ? (
          <Notice error>
            {error}{" "}
            <button className="text-button" onClick={() => setRetry(retry + 1)}>
              Thử lại
            </button>{" "}
            <Link href="/products">Về danh mục</Link>
          </Notice>
        ) : (
          <Loading />
        )}
      </div>
    );
  const max = Math.min(99, product.available_quantity);
  const available = product.is_active && max > 0;
  const valid = Number.isInteger(quantity) && quantity >= 1 && quantity <= max;
  const copy = productCopy(product);
  const selected = product.images[image];
  return (
    <div className="container product-detail-page">
      <nav className="product-breadcrumb" aria-label="Đường dẫn sản phẩm">
        <Link href="/">Trang chủ</Link>
        <span aria-hidden="true">/</span>
        <Link href={`/products?category_id=${product.category.id}`}>
          {product.category.name}
        </Link>
        <span aria-hidden="true">/</span>
        <span aria-current="page">{product.name}</span>
      </nav>
      <article className="product-detail-panel">
        <div className="product-gallery">
          <div className="product-detail-cover">
            {selected ? (
              <ProductPicture
                product={{ ...product, images: [selected] }}
                sizes="(max-width: 760px) 90vw, 45vw"
                priority
              />
            ) : (
              <Icon name="book" size={96} />
            )}
          </div>
          {product.images.length > 1 && (
            <div className="product-thumbnails" aria-label="Ảnh sản phẩm">
              {product.images.map((item, index) => (
                <button
                  key={item.id}
                  type="button"
                  onClick={() => setImage(index)}
                  aria-label={`Xem ảnh ${index + 1}`}
                  aria-pressed={image === index}
                >
                  <ProductPicture
                    product={{ ...product, images: [item] }}
                    sizes="64px"
                  />
                </button>
              ))}
            </div>
          )}
        </div>
        <div className="product-detail-info">
          <p className="detail-eyebrow">{product.category.name}</p>
          <h1>{product.name}</h1>
          <dl className="product-specifications">
            {copy.author && (
              <div>
                <dt>Tác giả</dt>
                <dd>{copy.author}</dd>
              </div>
            )}
            {copy.publisher && (
              <div>
                <dt>NXB / Thương hiệu</dt>
                <dd>{copy.publisher}</dd>
              </div>
            )}
            <div>
              <dt>Mã sản phẩm</dt>
              <dd>{product.sku}</dd>
            </div>
            <div>
              <dt>Tình trạng</dt>
              <dd>{available ? "Còn hàng" : "Tạm hết hàng"}</dd>
            </div>
            {product.grade && (
              <div>
                <dt>Khối lớp</dt>
                <dd>Lớp {product.grade}</dd>
              </div>
            )}
          </dl>
          <div className="detail-price-panel">
            <ProductPrice product={product} />
          </div>
          <div className="detail-quantity-block">
            <label htmlFor="detail-quantity">Số lượng</label>
            <div className="detail-quantity">
              <button
                aria-label="Giảm số lượng"
                disabled={quantity <= 1 || !available}
                onClick={() => setQuantity(quantity - 1)}
              >
                −
              </button>
              <input
                id="detail-quantity"
                aria-label="Số lượng"
                type="number"
                min={1}
                max={Math.max(1, max)}
                value={quantity}
                disabled={!available}
                aria-invalid={available && !valid}
                aria-describedby={
                  available && !valid ? "detail-quantity-error" : undefined
                }
                onChange={(event) => setQuantity(Number(event.target.value))}
              />
              <button
                aria-label="Tăng số lượng"
                disabled={quantity >= max || !available}
                onClick={() => setQuantity(quantity + 1)}
              >
                +
              </button>
            </div>
            {available && !valid && (
              <p id="detail-quantity-error" role="alert" className="error-text">
                Nhập số nguyên từ 1 đến {max}.
              </p>
            )}
          </div>
          <ProductActions
            product={product}
            quantity={quantity}
            expanded
            className="detail-actions"
          />
          <a className="detail-read-more" href="#product-description">
            Đọc mô tả sản phẩm <span aria-hidden="true">↓</span>
          </a>
        </div>
      </article>
      <section
        id="product-description"
        className="product-detail-description"
        aria-labelledby="product-description-heading"
      >
        <h2 id="product-description-heading">Mô tả sản phẩm</h2>
        <ProductDescription product={product} />
      </section>
    </div>
  );
}
