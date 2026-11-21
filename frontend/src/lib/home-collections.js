// Priority follows the page order. Never fabricate items to fill a collection.
export function homeCollections(
  home,
  catalog,
  { newLimit = 10, recommendedLimit = 15 } = {},
) {
  const used = new Set();
  function take(items, count) {
    const result = [];
    for (const item of items) {
      if (result.length >= count) break;
      if (used.has(item.id)) continue;
      used.add(item.id);
      result.push(item);
    }
    return result;
  }
  return {
    ...home,
    bestseller: take(home.bestseller, 10),
    new: take(catalog, newLimit),
    recommended: take([...home.recommended, ...catalog], recommendedLimit),
  };
}
