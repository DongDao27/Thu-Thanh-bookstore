import { HomeBanners } from "@/components/home-banners";
import HomeStorefront from "@/components/home-storefront";
import { BrandCarousel } from "@/components/brand-carousel";
export default function HomePage() {
  return (
    <>
      <div className="container clicon-home">
        <HomeBanners />
        <HomeStorefront />
      </div>
      <BrandCarousel />
    </>
  );
}
