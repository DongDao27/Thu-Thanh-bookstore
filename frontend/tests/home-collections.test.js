import { test } from "node:test";
import assert from "node:assert/strict";
import { homeCollections } from "../src/lib/home-collections.js";
test("home collections prioritize bestsellers and never repeat IDs across feeds", () => {
  const catalog = Array.from({ length: 70 }, (_, id) => ({ id }));
  const home = {
    bestseller: [catalog[2], catalog[2], catalog[1]],
    recommended: [catalog[60], catalog[2], catalog[61]],
  };
  const result = homeCollections(home, catalog);
  const ids = [...result.bestseller, ...result.new, ...result.recommended].map(
    (p) => p.id,
  );
  assert.equal(new Set(ids).size, ids.length);
  assert.deepEqual(
    result.bestseller.map((p) => p.id),
    [2, 1],
  );
  assert.equal(result.new.length, 10);
  assert.equal(result.recommended.length, 15);
  assert.equal(result.recommended[0].id, 60);
  assert.equal(home.bestseller.length, 3);
});
test("small catalog stays truthful and preserves overflow candidates for later feeds", () => {
  const items = [{ id: 1 }, { id: 2 }, { id: 3 }];
  const result = homeCollections({ bestseller: [], recommended: [] }, items, {
    newLimit: 2,
  });
  assert.deepEqual(result.new, items.slice(0, 2));
  assert.deepEqual(result.recommended, [items[2]]);
});
