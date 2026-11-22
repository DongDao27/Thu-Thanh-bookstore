import Link from "next/link";
import Image from "next/image";

// Public, static content renders without waiting for client auth or catalog.
export function HomeBanners() {
  return (
    <>
      <section className="reference-hero" aria-label="Thế giới truyện tranh">
        <Image
          src="/banners/phuongnam-manga.png"
          alt="Thế giới manga và truyện tranh"
          width={1920}
          height={600}
          priority
          fetchPriority="high"
          style={{ width: "100%", height: "auto", aspectRatio: "1920 / 600" }}
          sizes="(max-width: 1400px) 96vw, 1320px"
        />
        <h1 className="sr-only">
          Nhà sách Thu Thanh — Sách và đồ dùng học tập
        </h1>
      </section>
      <section className="editorial-banners" aria-label="Sách dành cho bạn">
        <Link
          href="/products"
          className="editorial-banner reference-banner"
          aria-label="Xem danh mục sách mới"
        >
          <Image
            src="/banners/phuongnam-sach-moi.png"
            loading="eager"
            fetchPriority="high"
            style={{ width: "100%", height: "auto", aspectRatio: "900 / 506" }}
            alt="Sách mới tháng này"
            width={900}
            height={506}
            sizes="(max-width: 760px) 94vw, 48vw"
          />
        </Link>
        <Link
          href="/products"
          className="editorial-banner reference-banner"
          aria-label="Xem bộ sưu tập sách"
        >
          <Image
            src="/banners/phuongnam-new-books.png"
            alt="Bộ sưu tập sách mới"
            loading="eager"
            style={{ width: "100%", height: "auto", aspectRatio: "900 / 506" }}
            width={900}
            height={506}
            sizes="(max-width: 760px) 94vw, 48vw"
          />
        </Link>
      </section>
    </>
  );
}
