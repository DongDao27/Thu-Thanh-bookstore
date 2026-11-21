"use client";
import { useId, useState } from "react";
import Link from "next/link";
import { ProductDescription, productCopy } from "./product-description";
import { ProductActions } from "./product-actions";
import { ProductPicture } from "./product-picture";
import { ProductPrice } from "./product-price";
import { FlameIcon } from "./flame-icon";
import { Icon } from "./ui";
export function BestsellerShowcase({ products }) {
  const [selected, setSelected] = useState(products[0]?.id);
  const detailId = useId();
  const titleId = useId();
  const product = products.find((item) => item.id === selected) ?? products[0];
  if (!product) return null;
  return (
    <div className="bestseller-showcase">
      <div className="bestseller-rank-panel">
        <p className="ranking-hint">
          Top {Math.min(products.length, 10)} · Cuộn xuống để xem các hạng tiếp
          theo
        </p>
        <ol
          className="bestseller-ranking"
          aria-label="Xếp hạng bán chạy"
          tabIndex={0}
        >
          {products.slice(0, 10).map((item, index) => (
            <li key={item.id}>
              <button
                className="bestseller-rank-button"
                aria-label={`Top ${index + 1}: ${item.name}`}
                aria-pressed={product.id === item.id}
                aria-controls={detailId}
                onClick={() => {
                  setSelected(item.id);
                }}
                onKeyDown={(event) => {
                  if (
                    !["ArrowDown", "ArrowUp", "Home", "End"].includes(event.key)
                  )
                    return;
                  event.preventDefault();
                  const next =
                    event.key === "Home"
                      ? 0
                      : event.key === "End"
                        ? products.length - 1
                        : (index +
                            (event.key === "ArrowDown" ? 1 : -1) +
                            products.length) %
                          products.length;
                  setSelected(products[next].id);
                  const buttons = event.currentTarget
                    .closest("ol")
                    ?.querySelectorAll("button");
                  buttons?.[next].focus();
                }}
              >
                <span className="bestseller-rank-number" aria-hidden="true">
                  {index + 1}
                </span>
                <span className="bestseller-thumbnail">
                  {item.images.length ? (
                    <ProductPicture product={item} />
                  ) : (
                    <Icon name="book" size={32} />
                  )}
                </span>
                <span className="bestseller-rank-copy">
                  <span className="bestseller-top">
                    <FlameIcon /> Top {index + 1}
                  </span>
                  <span className="bestseller-name">{item.name}</span>
                  {Number(item.sold_count) > 0 && (
                    <span className="muted">
                      Đã bán {item.sold_count.toLocaleString("vi-VN")}
                    </span>
                  )}
                </span>
              </button>
            </li>
          ))}
        </ol>
      </div>
      <section
        id={detailId}
        className="bestseller-detail"
        aria-labelledby={titleId}
      >
        <div className="bestseller-product-main">
          <Link
            className="bestseller-cover"
            aria-label={`Xem thông tin ${product.name}`}
            href={`/products/${product.id}`}
          >
            {product.images.length ? (
              <ProductPicture product={product} loading="eager" />
            ) : (
              <Icon name="book" size={64} />
            )}
          </Link>
          <div className="bestseller-product-copy">
            <h3 id={titleId}>
              <Link href={`/products/${product.id}`}>{product.name}</Link>
            </h3>
            <p className="muted">{product.category.name}</p>
            <p className="bestseller-sku">Mã sản phẩm: {product.sku}</p>
            {productCopy(product).author && (
              <p className="bestseller-author">
                Tác giả: {productCopy(product).author}
              </p>
            )}
            {Number(product.sold_count) > 0 && (
              <p className="muted">
                Đã bán {product.sold_count.toLocaleString("vi-VN")}
              </p>
            )}
            <ProductPrice product={product} />
            <ProductActions product={product} className="bestseller-add" />
          </div>
        </div>
        <div className="bestseller-description">
          <h4>Mô tả sản phẩm</h4>
          <ProductDescription product={product} />
          <Link className="detail-read-more" href={`/products/${product.id}`}>
            Xem chi tiết sản phẩm <span aria-hidden="true">→</span>
          </Link>
        </div>
      </section>
    </div>
  );
}
