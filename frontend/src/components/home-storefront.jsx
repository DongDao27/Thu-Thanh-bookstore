"use client";
import Link from "next/link";
import { useEffect, useState } from "react";
import { api } from "@/lib/api-client";
import { homeCollections } from "@/lib/home-collections";
import { errorMessage } from "@/lib/format";
import { ProductCard } from "./product-card";
import { BestsellerShowcase } from "./bestseller-showcase";
import { FlameIcon } from "./flame-icon";
import { useStore } from "./store-provider";
import { publicStorefront } from "@/lib/public-storefront";
import { CampaignBanner } from "./campaign-banner";
import { Empty, Icon, Loading, Notice } from "./ui";
export default function HomeStorefront() {
  const { user, authLoading } = useStore();
  const [home, setHome] = useState(null);
  const [error, setError] = useState("");
  const [retry, setRetry] = useState(0);
  useEffect(() => {
    if (authLoading) return;
    const abort = new AbortController();
    setError("");
    async function load() {
      const result = await (user?.role === "customer"
        ? api("/home", { signal: abort.signal })
        : publicStorefront());
      // The 40-item public catalog shares the server snapshot cache. The fallback
      // allows the frontend to roll out before the additive backend contract.
      const catalog =
        result.data.catalog ??
        (
          await api("/products?sort=newest&per_page=40", {
            signal: abort.signal,
          })
        ).data;
      if (!abort.signal.aborted) setHome(homeCollections(result.data, catalog));
    }
    load().catch((error) => {
      if (!abort.signal.aborted) setError(errorMessage(error));
    });
    return () => abort.abort();
  }, [retry, user?.id, user?.role, authLoading]);
  return (
    <>
      {error && (
        <Notice error>
          {error}{" "}
          <button className="text-button" onClick={() => setRetry(retry + 1)}>
            Thử lại
          </button>
        </Notice>
      )}
      {home ? (
        <>
          <Collection
            title="Bán chạy nhất"
            products={home.bestseller}
            href="/products?section=bestseller"
            emptyTitle="Chưa có sản phẩm trong mục Bán chạy nhất"
          />
          <CampaignBanner id="manga" />
          <Collection
            title="Sản phẩm mới"
            products={home.new}
            href="/products?sort=newest"
          />
          <CampaignBanner id="kids" />
          <Collection
            title="Gợi ý cho bạn"
            products={home.recommended}
            href="/products"
            recommended
          />
        </>
      ) : (
        <section
          className="home-loading"
          style={{ minHeight: "100vh" }}
          aria-label="Đang tải sản phẩm"
          aria-busy="true"
        >
          <Loading />
        </section>
      )}
    </>
  );
}
function SectionHeading({ title, href }) {
  const description = {
    "Bán chạy nhất": "Những cuốn sách và sản phẩm nổi bật tại Thu Thanh.",
    "Sản phẩm mới": "Vừa có mặt trên kệ sách Thu Thanh.",
    "Gợi ý cho bạn": "Thêm một lựa chọn cho lần đọc tiếp theo.",
  }[title];
  return (
    <div className="section-heading">
      <div className="collection-heading-copy">
        <h2
          className={title === "Bán chạy nhất" ? "bestseller-title" : undefined}
        >
          {title === "Bán chạy nhất" && <FlameIcon />}
          {title}
        </h2>
        {description && <p>{description}</p>}
      </div>
      <Link href={href} className="collection-link">
        Xem tất cả <Icon name="arrow" size={18} />
      </Link>
    </div>
  );
}
function Collection({
  title,
  products,
  href,
  recommended = false,
  emptyTitle = "Chưa có sản phẩm",
}) {
  return (
    <section
      className={`home-collection${recommended ? " recommendations" : ""}`}
    >
      <SectionHeading title={title} href={href} />
      {products.length ? (
        title === "Bán chạy nhất" ? (
          <BestsellerShowcase products={products} />
        ) : (
          <div className="product-grid grid grid-cols-1 min-[421px]:grid-cols-2 min-[761px]:grid-cols-3 min-[1021px]:grid-cols-4 xl:grid-cols-5 gap-3">
            {products.map((product) => (
              <ProductCard key={product.id} product={product} />
            ))}
          </div>
        )
      ) : (
        <Empty title={emptyTitle}>
          <Link href="/products">Xem danh mục sản phẩm</Link>
        </Empty>
      )}
    </section>
  );
}
