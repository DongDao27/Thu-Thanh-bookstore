"use client";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { useRef } from "react";
// Catalog grids have dozens of links. Warm only the route the reader intends to open.
export function IntentLink({ href, children, ...props }) {
  const router = useRouter();
  const warmed = useRef(null);
  function prefetch() {
    const connection = navigator.connection;
    if (
      connection?.saveData ||
      /(^|-)2g$/.test(connection?.effectiveType ?? "")
    )
      return;
    if (warmed.current === href) return;
    warmed.current = href;
    router.prefetch(href, {
      onInvalidate: () => {
        warmed.current = null;
      },
    });
  }
  return (
    <Link
      {...props}
      href={href}
      prefetch={false}
      onMouseEnter={prefetch}
      onFocus={prefetch}
      onTouchStart={prefetch}
    >
      {children}
    </Link>
  );
}
