import {
  readdirSync,
  existsSync,
  mkdirSync,
  writeFileSync,
  statSync,
  readFileSync,
} from 'fs';
import {resolve, extname, basename, join, relative, dirname} from 'path';
import ttf2woff from 'ttf2woff';
import ttf2woff2 from 'ttf2woff2';

const FONTS_DIR = 'resources/fonts';
const OUTPUT_DIR = 'public/fonts';

// Создаем output директорию если не существует
if (!existsSync(OUTPUT_DIR)) {
  mkdirSync(OUTPUT_DIR, {recursive: true});
}

// Рекурсивный поиск шрифтов с сохранением структуры
function findFontFiles(dir) {
  let fontFiles = [];

  if (!existsSync(dir)) return fontFiles;

  const items = readdirSync(dir);

  for (const item of items) {
    const fullPath = join(dir, item);
    const stat = statSync(fullPath);

    if (stat.isDirectory()) {
      // Рекурсивно ищем в подпапках
      fontFiles = fontFiles.concat(findFontFiles(fullPath));
    } else if (item.endsWith('.ttf') || item.endsWith('.otf')) {
      fontFiles.push(fullPath);
    }
  }

  return fontFiles;
}

function convertFont(inputPath, outputBaseDir) {
  const fileName = basename(inputPath, extname(inputPath));

  // Получаем относительный путь от src/fonts
  const relativePath = relative(FONTS_DIR, dirname(inputPath));
  const outputDir = relativePath
    ? join(outputBaseDir, relativePath)
    : outputBaseDir;

  // Создаем выходную директорию если не существует
  if (!existsSync(outputDir)) {
    mkdirSync(outputDir, {recursive: true});
  }

  try {
    console.log(`Обработка файла: ${inputPath}`);

    // Читаем исходный шрифт
    const fontBuffer = readFileSync(inputPath);

    // Конвертация в WOFF2 (лучшее сжатие)
    const woff2Output = resolve(outputDir, `${fileName}.woff2`);
    const woff2 = ttf2woff2(fontBuffer);
    writeFileSync(woff2Output, Buffer.from(woff2));
    console.log(
      `✅ Converted: ${relativePath ? relativePath + '/' : ''}${fileName}.woff2`
    );

    // Конвертация в WOFF (поддержка старых браузеров)
    const woffOutput = resolve(outputDir, `${fileName}.woff`);
    const woff = ttf2woff(fontBuffer).buffer;
    writeFileSync(woffOutput, Buffer.from(woff));
    console.log(
      `✅ Converted: ${relativePath ? relativePath + '/' : ''}${fileName}.woff`
    );

    return {
      name: fileName,
      path: relativePath ? `${relativePath}/${fileName}` : fileName,
      family: relativePath || 'Default',
    };
  } catch (error) {
    console.error(`❌ Error converting ${fileName}:`, error.message);
    return null;
  }
}

function generateFontCSS(convertedFonts) {
  // Группируем шрифты по семействам (папкам)
  const fontFamilies = {};

  convertedFonts.forEach((font) => {
    if (!font) return;

    if (!fontFamilies[font.family]) {
      fontFamilies[font.family] = [];
    }
    fontFamilies[font.family].push(font);
  });

  // Генерируем @font-face декларации
  const fontFaces = convertedFonts
    .filter(Boolean)
    .map((font) => {
      return `@font-face {
  font-family: '${font.family}';
  src: url('/fonts/${font.path}.woff2') format('woff2'),
       url('/fonts/${font.path}.woff') format('woff');
  font-display: swap;
}`;
    })
    .join('\n\n');

  return fontFaces;
}

// Основная функция
function convertFonts() {
  if (!existsSync(FONTS_DIR)) {
    console.log('📁 Creating fonts directory...');
    mkdirSync(FONTS_DIR, {recursive: true});
    console.log('Put your .ttf or .otf files in src/fonts/ directory');
    return;
  }

  const fontFiles = findFontFiles(FONTS_DIR);

  if (fontFiles.length === 0) {
    console.log('No font files found in src/fonts/ (including subdirectories)');
    return;
  }

  console.log(`🔄 Converting ${fontFiles.length} font(s)...`);
  console.log(
    'Found fonts:',
    fontFiles.map((f) => basename(f))
  );

  const convertedFonts = fontFiles.map((file) => {
    return convertFont(file, OUTPUT_DIR);
  });

  // // Генерируем CSS для шрифтов
  // const cssContent = generateFontCSS(convertedFonts);

  // // Создаем scss директорию если не существует
  // if (!existsSync('src/scss/common')) {
  //   mkdirSync('src/scss/common', { recursive: true });
  // }

  // writeFileSync('src/scss/common/_fonts.scss', cssContent);

  console.log('🎉 Font conversion completed!');
  // console.log('📝 Generated _fonts.scss with @font-face declarations');
}

convertFonts();
