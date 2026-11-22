"use client";
import Image, { getImageProps } from "next/image";
import { useEffect, useRef, useState } from "react";
import bounds from "@/lib/product-bounds.json";
import { imageSrc } from "@/lib/product-images";
export function ProductPicture({
  product,
  priority = false,
  loading,
  sizes = "(max-width: 420px) 92vw, (max-width: 700px) 44vw, (max-width: 1100px) 30vw, (max-width: 1279px) 23vw, (max-width: 1799px) 16vw, 12vw",
}) {
  const src = imageSrc(product.images[0].url);
  const crop = bounds[src];
  if (crop)
    return (
      <CroppedPicture
        crop={crop}
        src={src}
        product={product}
        priority={priority}
        loading={loading}
      />
    );
  return (
    <Image
      src={src}
      alt={product.images[0].alt_text ?? product.name}
      fill
      sizes={sizes}
      style={{ objectFit: "contain" }}
      priority={priority}
      loading={loading}
    />
  );
}

function CroppedPicture({ crop, src, product, priority, loading }) {
  const ref = useRef(null);
  const [visible, setVisible] = useState(priority || loading === "eager");
  useEffect(() => {
    if (visible) return;
    if (!window.IntersectionObserver) {
      setVisible(true);
      return;
    }
    const observer = new IntersectionObserver(
      ([entry]) => {
        if (entry.isIntersecting) {
          setVisible(true);
          observer.disconnect();
        }
      },
      { rootMargin: "240px" },
    );
    observer.observe(ref.current);
    return () => observer.disconnect();
  }, [visible]);
  // SVG <image> has no native lazy loading or responsive srcset. Keep its
  // existing crop, but defer offscreen requests and use Next's image optimizer.
  const { props } = getImageProps({
    src,
    alt: "",
    width: priority ? crop.width : Math.min(crop.width, 320),
    height: priority
      ? crop.height
      : Math.round((crop.height * Math.min(crop.width, 320)) / crop.width),
  });
  return (
    <svg
      ref={ref}
      className="product-picture"
      viewBox={crop.viewBox}
      role="img"
      aria-label={product.images[0].alt_text ?? product.name}
      preserveAspectRatio="xMidYMid meet"
    >
      <image
        href={visible ? props.src : undefined}
        width={crop.width}
        height={crop.height}
      />
    </svg>
  );
}
