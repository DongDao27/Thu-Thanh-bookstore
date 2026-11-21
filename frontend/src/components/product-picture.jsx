import Image from "next/image";
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
      <svg
        className="product-picture"
        viewBox={crop.viewBox}
        role="img"
        aria-label={product.images[0].alt_text ?? product.name}
        preserveAspectRatio="xMidYMid meet"
      >
        <image href={src} width={crop.width} height={crop.height} />
      </svg>
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
