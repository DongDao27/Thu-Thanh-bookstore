export function productCopy(product) {
  const lines = (product.description ?? "").split("\n");
  const author = lines
    .find((line) => line.startsWith("Tác giả:"))
    ?.slice(8)
    .trim();
  const publisher = lines
    .find((line) => line.startsWith("NXB / Thương hiệu:"))
    ?.split(":")
    .slice(1)
    .join(":")
    .trim();
  const text = lines
    .filter((line) => !/^(Nhóm|Tác giả|NXB \/ Thương hiệu):/.test(line))
    .join("\n")
    .trim();
  return { author, publisher, text };
}
export function ProductDescription({ product }) {
  const { text } = productCopy(product);
  return (
    <div className="product-description-copy">
      {(
        text ||
        `${product.name} thuộc danh mục ${product.category.name.toLowerCase()}.`
      )
        .split(/\n\s*\n/)
        .map((paragraph, index) => (
          <p key={index}>{paragraph}</p>
        ))}
    </div>
  );
}
