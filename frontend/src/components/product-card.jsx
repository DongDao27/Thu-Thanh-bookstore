"use client";
import { ProductActions } from "./product-actions";
import Link from "next/link";
import { ProductPicture } from "./product-picture";
import { imageSrc } from "@/lib/product-images";
export { imageSrc } from "@/lib/product-images";
import { ProductPrice } from "./product-price";
import { Icon } from "./ui";
export function ProductCard({ product, priority = false, featured = false }) {
  const image = product.images[0];
  const available = product.is_active && product.available_quantity > 0;
  return (
    <article className={`product-card${featured ? " featured-product" : ""}`}>
      <Link
        className="product-image"
        href={`/products/${product.id}`}
        aria-label={`Xem chi tiết ${product.name}`}
      >
        {image && imageSrc(image.url) ? (
          <ProductPicture product={product} priority={priority} />
        ) : (
          <Icon name="book" size={64} />
        )}
      </Link>
      <p className="product-category">{product.category.name}</p>
      <h3>
        <Link href={`/products/${product.id}`}>{product.name}</Link>
      </h3>
      <div className="product-bottom">
        <ProductPrice product={product} />
      </div>
      {product.sold_count !== undefined && product.sold_count > 0 && (
        <small className="sold-count">
          Đã bán {product.sold_count.toLocaleString("vi-VN")}
        </small>
      )}
      {!available && <span className="muted stock-label">Tạm hết hàng</span>}
      <ProductActions product={product} />
    </article>
  );
}
