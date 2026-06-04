export default function fluidType(
    minViewport,
    maxViewport,
    minValue,
    maxValue,
    negative = false,
) {
    const clean = (str) => str.toString().trim().replace(/['"]/g, '');

    const minVw = clean(minViewport);
    const maxVw = clean(maxViewport);
    const minVal = clean(minValue);
    const maxVal = clean(maxValue);

    const minNum = parseFloat(minVal);
    const maxNum = parseFloat(maxVal);
    const minVwNum = parseFloat(minVw);
    const maxVwNum = parseFloat(maxVw);

    const minUnit = minVal.replace(minNum, '');
    const maxUnit = maxVal.replace(maxNum, '');

    if (negative) {
        return `calc(-1 * clamp(${minVal}, calc(${minVal} + (${maxNum} - ${minNum}) * (100vw - ${minVw}) / (${maxVwNum} - ${minVwNum})), ${maxVal}))`;
    }

    // Если это проценты, делаем ВСЁ в процентах
    if (minUnit === '%' && maxUnit === '%') {
        // Убираем % из разности для вычислений
        const difference = maxNum - minNum;

        return `clamp(${minVal}, calc(${minVal} + (${difference}) * (100vw - ${minVw}) / (${maxVwNum} - ${minVwNum})), ${maxVal})`;
    }

    // Для всех остальных случаев
    return `clamp(${minVal}, calc(${minVal} + (${maxNum} - ${minNum}) * (100vw - ${minVw}) / (${maxVwNum} - ${minVwNum})), ${maxVal})`;
}