// Prerender public content; account, price and stock remain live client/API reads.
export const dynamic = "force-static";
export const revalidate = 3600;
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
