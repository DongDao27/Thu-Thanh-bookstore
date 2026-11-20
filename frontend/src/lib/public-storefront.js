import { api } from "./api-client";
let pending;
// Coalesce simultaneous home/header reads; do not store any account or personalized data.
export function publicStorefront() {
  pending ??= api("/storefront").finally(() => {
    pending = undefined;
  });
  return pending;
}
