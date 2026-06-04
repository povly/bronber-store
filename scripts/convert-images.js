import {readdirSync, existsSync, mkdirSync, statSync} from 'fs';
import {join, extname, basename, dirname, relative} from 'path';
import sharp from 'sharp';
import {fileURLToPath} from 'url';
import {dirname as pathDirname} from 'path';

const __filename = fileURLToPath(import.meta.url);
const __dirname = pathDirname(__filename);

const SUPPORTED_FORMATS = [
  '.jpg',
  '.jpeg',
  '.png',
  '.webp',
  '.tiff',
  '.gif',
  '.bmp',
];
const srcDir = 'resources/images';
const outputDir = 'public/images';

// Получаем аргументы командной строки
const args = process.argv.slice(2);
const targetPath = args[0]; // путь к папке или файлу

async function ensureDir(dirPath) {
  if (!existsSync(dirPath)) {
    mkdirSync(dirPath, {recursive: true});
  }
}

async function convertImage(inputPath, outputDir, filename) {
  const baseName = basename(filename, extname(filename));
  const relativePath = relative(srcDir, dirname(inputPath));
  const targetDir = join(outputDir, relativePath);

  await ensureDir(targetDir);

  const outputPaths = {
    avif: join(targetDir, `${baseName}.avif`),
    webp: join(targetDir, `${baseName}.webp`),
    original: join(targetDir, filename),
  };

  try {
    const image = sharp(inputPath);

    // Конвертация в AVIF (лучшее сжатие)
    await image
      .clone()
      .avif({
        quality: 80,
        effort: 4,
        chromaSubsampling: '4:2:0',
      })
      .toFile(outputPaths.avif);

    // Конвертация в WebP (широкая поддержка)
    await image
      .clone()
      .webp({
        quality: 85,
        effort: 4,
        method: 6,
      })
      .toFile(outputPaths.webp);

    // Оптимизация оригинального формата
    const ext = extname(filename).toLowerCase();
    if (ext === '.jpg' || ext === '.jpeg') {
      await image
        .clone()
        .jpeg({
          quality: 90,
          progressive: true,
          mozjpeg: true,
        })
        .toFile(outputPaths.original);
    } else if (ext === '.png') {
      await image
        .clone()
        .png({
          quality: 90,
          compressionLevel: 9,
          progressive: true,
        })
        .toFile(outputPaths.original);
    } else {
      // Для других форматов просто копируем оптимизированную версию
      await image.clone().toFile(outputPaths.original);
    }

    console.log(`✅ Обработано: ${filename}`);
    return true;
  } catch (error) {
    console.error(`❌ Ошибка обработки ${filename}:`, error.message);
    return false;
  }
}

async function processDirectory(dirPath) {
  if (!existsSync(dirPath)) {
    console.log(
      `📁 Папка ${dirPath} не найдена. Создайте её и добавьте изображения.`
    );
    return;
  }

  const items = readdirSync(dirPath);
  let totalFiles = 0;
  let processedFiles = 0;

  for (const item of items) {
    const itemPath = join(dirPath, item);
    const stat = statSync(itemPath);

    if (stat.isDirectory()) {
      // Рекурсивно обрабатываем подпапки
      await processDirectory(itemPath);
    } else if (stat.isFile()) {
      const ext = extname(item).toLowerCase();
      if (SUPPORTED_FORMATS.includes(ext)) {
        totalFiles++;
        const success = await convertImage(itemPath, outputDir, item);
        if (success) processedFiles++;
      }
    }
  }

  if (totalFiles > 0) {
    console.log(
      `\n📊 Обработано ${processedFiles}/${totalFiles} изображений в ${dirPath}`
    );
  }
}

async function processSpecificFile(filePath) {
  if (!existsSync(filePath)) {
    console.error(`❌ Файл не найден: ${filePath}`);
    return false;
  }

  const stat = statSync(filePath);
  if (!stat.isFile()) {
    console.error(`❌ Указанный путь не является файлом: ${filePath}`);
    return false;
  }

  const ext = extname(filePath).toLowerCase();
  if (!SUPPORTED_FORMATS.includes(ext)) {
    console.error(`❌ Неподдерживаемый формат: ${ext}`);
    console.log(`Поддерживаемые форматы: ${SUPPORTED_FORMATS.join(', ')}`);
    return false;
  }

  const filename = basename(filePath);
  console.log(`🖼️  Конвертируем файл: ${filename}`);

  const success = await convertImage(filePath, outputDir, filename);
  if (success) {
    console.log(`✅ Файл успешно обработан: ${filename}`);
  }

  return success;
}

async function processSpecificDirectory(dirPath) {
  if (!existsSync(dirPath)) {
    console.error(`❌ Папка не найдена: ${dirPath}`);
    return;
  }

  const stat = statSync(dirPath);
  if (!stat.isDirectory()) {
    console.error(`❌ Указанный путь не является папкой: ${dirPath}`);
    return;
  }

  console.log(`📁 Обрабатываем папку: ${dirPath}`);
  await processDirectory(dirPath);
}

async function main() {
  console.log('🖼️  Начинаем конвертацию изображений...\n');

  await ensureDir(outputDir);

  if (targetPath) {
    // Проверяем существование пути
    if (!existsSync(targetPath)) {
      console.error(`❌ Путь не найден: ${targetPath}`);
      console.log('\n💡 Использование:');
      console.log('   node convert-images.js [путь]');
      console.log(
        '    /photo.jpg    # конкретный файл'
      );
      console.log(
        '   node convert-images.js src/images/gallery     # конкретная папка'
      );
      console.log(
        '   node convert-images.js                        # вся папка src/images'
      );
      return;
    }

    const stat = statSync(targetPath);

    if (stat.isFile()) {
      // Обрабатываем конкретный файл
      await processSpecificFile(targetPath);
    } else if (stat.isDirectory()) {
      // Обрабатываем конкретную папку
      await processSpecificDirectory(targetPath);
    }
  } else {
    // Обрабатываем всю папку src/images
    console.log(`📁 Обрабатываем всю папку: ${srcDir}`);
    await processDirectory(srcDir);
  }

  console.log('\n✨ Конвертация завершена!');
  console.log(`📁 Результат: ${outputDir}`);
  console.log('\n💡 Для каждого изображения создано:');
  console.log('   📸 .avif - современный формат (лучшее сжатие)');
  console.log('   🌐 .webp - широкая поддержка');
  console.log('   🔄 оригинальный формат (оптимизированный)');
}

main().catch(console.error);
