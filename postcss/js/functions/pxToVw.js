/**
 * Преобразует px в vw относительно заданной ширины макета.
 * Использование в CSS: font-size: pxToVw(300, 540);
 * @param {number} px — значение в пикселях
 * @param {number} [layoutWidth=1920] — ширина макета (по умолчанию 1920)
 * @returns {string} — значение в vw, например "55.5556vw"
 */
export default function pxToVw(px, layoutWidth = 1920) {
  // Убедимся, что значения — числа
  const value = parseFloat(px);
  const width = parseFloat(layoutWidth);

  if (isNaN(value) || isNaN(width) || width <= 0) {
    throw new Error('pxToVw: оба аргумента должны быть числами, ширина макета > 0');
  }

  const vw = (value / width) * 100;
  // Округлим до 4 знаков (как обычно делает CSS)
  return `${vw.toFixed(4)}vw`;
}
