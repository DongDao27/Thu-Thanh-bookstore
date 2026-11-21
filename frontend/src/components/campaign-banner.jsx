"use client";
import Link from "next/link";
import { getImageProps } from "next/image";
import banners from "@/lib/store-banners.json";
import { useStore } from "./store-provider";
export function CampaignBanner({ id }) {
  const { categories } = useStore();
  const banner = banners[id];
  const category = categories.find((item) => item.slug === banner.categorySlug);
  const href = category ? `/products?category_id=${category.id}` : "/products";
  const header = id === "header";
  const sizes = header
    ? "(max-width: 700px) 100vw, 460px"
    : "(max-width: 1400px) 96vw, 1320px";
  const { props } = getImageProps({
    src: banner.src,
    width: banner.width,
    height: banner.height,
    alt: banner.alt,
    sizes,
    loading: "eager",
    fetchPriority: header ? "high" : "auto",
  });
  const mobile = banner.mobile
    ? getImageProps({ ...banner.mobile, alt: banner.alt, sizes: "96vw" }).props
    : null;
  return (
    <Link
      href={href}
      className={`campaign-banner ${header ? "header-campaign" : "collection-campaign"}`}
      aria-label={`${banner.alt} — xem sản phẩm`}
      style={{ "--campaign-background": banner.background }}
    >
      <picture>
        {mobile && (
          <source
            media="(max-width: 700px)"
            srcSet={mobile.srcSet}
            sizes={mobile.sizes}
            width={banner.mobile.width}
            height={banner.mobile.height}
          />
        )}
        <img {...props} />
      </picture>
    </Link>
  );
}
