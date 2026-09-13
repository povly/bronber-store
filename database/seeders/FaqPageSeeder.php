<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

/**
 * Editable FAQ page: publishes the DB page with slug «faq» so /faq
 * renders from MoonShine-editable flexible-layout blocks. Content
 * mirrors the static prototype (blocks/faq/faq.blade.php). Idempotent:
 * firstOrCreate by slug / (page_id, locale); an existing translation
 * with EMPTY content is filled with the demo data (e.g. a stub created
 * from the admin panel) — authored content is never overwritten.
 */
class FaqPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::query()->firstOrCreate(
            ['slug' => 'faq'],
            ['is_published' => true, 'sort_order' => 0],
        );

        $locales = [];

        foreach (['ru' => $this->contentRu(), 'en' => $this->contentEn()] as $locale => $content) {
            $translation = PageTranslation::query()->firstOrCreate(
                ['page_id' => $page->getKey(), 'locale' => $locale],
                $this->attributes($locale, $content),
            );

            if ($this->isEmptyContent($translation)) {
                $translation->fill($this->attributes($locale, $content))->save();

                $locales[] = $locale.':filled';

                continue;
            }

            $locales[] = $locale.($translation->wasRecentlyCreated ? ':created' : ':exists');
        }

        Log::info('[FaqPageSeeder] faq page seeded, locales={locales}', [
            'locales' => implode(', ', $locales),
        ]);
    }

    /**
     * Creation/fill attributes for a locale's demo translation.
     *
     * @param  list<array<string, mixed>>  $content
     * @return array<string, mixed>
     */
    private function attributes(string $locale, array $content): array
    {
        return [
            'title' => $locale === 'ru'
                ? 'Часто задаваемые вопросы'
                : 'Frequently Asked Questions',
            'meta_title' => $locale === 'ru'
                ? 'FAQ — Bronber: частые вопросы об автозапчастях и заказах'
                : 'FAQ — Bronber: frequently asked questions about auto parts and orders',
            'meta_description' => $locale === 'ru'
                ? 'Ответы на частые вопросы о заказе и доставке автозапчастей для BMW, Audi, Volkswagen: оформление заказа, сроки доставки, оплата, возврат и подбор запчастей.'
                : 'Answers to common questions about ordering and delivering auto parts for BMW, Audi, Volkswagen: placing an order, delivery times, payment, returns and parts selection.',
            'content' => $content,
        ];
    }

    /**
     * A freshly created translation always carries content; a pre-existing
     * one is only "empty" when it has no blocks at all.
     */
    private function isEmptyContent(PageTranslation $translation): bool
    {
        return ! $translation->wasRecentlyCreated
            && ($translation->content === null || $translation->content === []);
    }

    /**
     * FAQ blocks mirroring the static prototype (blocks/faq/faq.blade.php).
     *
     * @return list<array<string, mixed>>
     */
    private function contentRu(): array
    {
        return [
            [
                '_type' => 'faq-items',
                'title' => 'Часто задаваемые вопросы',
                'items' => [
                    ['_type' => 'item', 'question' => 'Как оформить заказ?', 'answer' => "Оформить заказ на сайте очень просто:\n\nНайдите необходимый товар через поиск или каталог.\nОткройте карточку товара и нажмите кнопку «В корзину».\nПерейдите в корзину и проверьте список выбранных товаров.\nНажмите «Оформить заказ».\nУкажите контактные данные, выберите способ доставки и оплаты.\nПодтвердите заказ.\n\nПосле оформления с вами свяжется менеджер для подтверждения наличия товара, уточнения стоимости доставки и деталей заказа.\n\nЕсли у вас возникли вопросы при оформлении, свяжитесь с нами по телефону или через форму обратной связи."],
                    ['_type' => 'item', 'question' => 'Сколько времени занимает доставка?', 'answer' => 'Доставка по Москве и Московской области осуществляется в течение 1–2 рабочих дней. Доставка в регионы России занимает от 3 до 7 рабочих дней в зависимости от удалённости региона и выбранной транспортной компании. Точное время доставки менеджер уточнит при подтверждении заказа.'],
                    ['_type' => 'item', 'question' => 'Какие способы оплаты доступны?', 'answer' => "Мы принимаем следующие способы оплаты:\n\nБанковские карты Visa, Mastercard, МИР.\nСБП (Система быстрых платежей).\nБезналичный расчёт для юридических лиц.\nНаложенный платёж при получении (для отправлений транспортными компаниями).\n\nВсе платежи защищены и обрабатываются через сертифицированный платёжный шлюз."],
                    ['_type' => 'item', 'question' => 'Можно ли вернуть товар?', 'answer' => "Да, вы можете вернуть товар в течение 14 дней с момента покупки при соблюдении следующих условий:\n\nТовар не был в эксплуатации и сохранил товарный вид.\nСохранена заводская упаковка, комплектация и маркировка.\nИмеется чек или иной документ, подтверждающий покупку.\n\nДля оформления возврата свяжитесь с нашим менеджером. Возврат денежных средств осуществляется в течение 5–10 рабочих дней."],
                    ['_type' => 'item', 'question' => 'Как подобрать запчасть?', 'answer' => "Подобрать запчасть можно несколькими способами:\n\nПо артикулу — введите артикул в строку поиска на сайте.\nПо VIN-коду — укажите VIN вашего автомобиля, и система подберёт совместимые детали.\nПо характеристикам — используйте фильтры в каталоге (бренд, модель, год выпуска).\n\nЕсли вы не уверены в выборе, наши менеджеры помогут подобрать нужную запчасть по телефону или через форму обратной связи."],
                    ['_type' => 'item', 'question' => 'Оригинальные ли запчасти в вашем магазине?', 'answer' => 'Все запчасти в нашем магазине являются оригинальными или сертифицированными аналогами от проверенных производителей. Каждая деталь поставляется напрямую от официальных дистрибьюторов и имеет подтверждающую документацию. Мы гарантируем качество и подлинность всего реализуемого товара.'],
                ],
            ],
        ];
    }

    /**
     * English translation of the same FAQ blocks.
     *
     * @return list<array<string, mixed>>
     */
    private function contentEn(): array
    {
        return [
            [
                '_type' => 'faq-items',
                'title' => 'Frequently Asked Questions',
                'items' => [
                    ['_type' => 'item', 'question' => 'How do I place an order?', 'answer' => "Placing an order on the site is very simple:\n\nFind the product you need using search or the catalog.\nOpen the product page and click the “Add to cart” button.\nGo to the cart and check the list of selected items.\nClick “Checkout”.\nEnter your contact details, choose delivery and payment methods.\nConfirm the order.\n\nAfter placing the order, a manager will contact you to confirm product availability, delivery cost and order details.\n\nIf you have any questions while ordering, contact us by phone or via the feedback form."],
                    ['_type' => 'item', 'question' => 'How long does delivery take?', 'answer' => 'Delivery within Moscow and the Moscow region takes 1–2 business days. Delivery to other regions of Russia takes from 3 to 7 business days depending on the region and the chosen carrier. The manager will specify the exact delivery time when confirming your order.'],
                    ['_type' => 'item', 'question' => 'What payment methods are available?', 'answer' => "We accept the following payment methods:\n\nBank cards Visa, Mastercard, MIR.\nSBP (Fast Payment System).\nBank transfer for legal entities.\nCash on delivery (for shipments by transport companies).\n\nAll payments are secure and processed through a certified payment gateway."],
                    ['_type' => 'item', 'question' => 'Can I return a product?', 'answer' => "Yes, you can return a product within 14 days of purchase provided that:\n\nThe product has not been used and retains its presentation.\nThe factory packaging, completeness and marking are preserved.\nYou have a receipt or other document confirming the purchase.\n\nTo arrange a return, contact our manager. Refunds are processed within 5–10 business days."],
                    ['_type' => 'item', 'question' => 'How do I choose the right part?', 'answer' => "You can pick a part in several ways:\n\nBy article number — enter the article in the search bar on the site.\nBy VIN code — specify your car's VIN and the system will select compatible parts.\nBy characteristics — use the catalog filters (brand, model, year of manufacture).\n\nIf you are not sure about your choice, our managers will help you pick the right part by phone or via the feedback form."],
                    ['_type' => 'item', 'question' => 'Are the parts in your store original?', 'answer' => 'All parts in our store are original or certified analogues from trusted manufacturers. Every item comes directly from official distributors and is backed by supporting documentation. We guarantee the quality and authenticity of everything we sell.'],
                ],
            ],
        ];
    }
}
