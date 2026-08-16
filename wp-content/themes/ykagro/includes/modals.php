<?php // Modals + popups. ?>
<?php // data-lenis-prevent on the wrapper: it is the scroll container, so it must
      // keep scrolling while Lenis is stopped by the modal lock. ?>
<div class="modal js-modal" data-lenis-prevent aria-hidden="true">
  <div class="modal__overlay js-modal-close"></div>

  <!-- Написати нам / заявка (Figma 685:1338) -->
  <div class="modal__panel modal__panel--form" data-modal="appointment" data-lenis-prevent>
    <button class="modal__close js-modal-close" type="button" aria-label="Закрити">
      <?php yka_icon( 'icons/close.svg' ); ?>
    </button>
    <div class="modal__body modal__body--center">
      <h2 class="modal__title h3 clr-black">Створюймо майбутнє<br>бізнесу разом.</h2>
      <?php get_template_part( 'template-parts/form-block' ); ?>
    </div>
  </div>

  <!-- Деталі напряму (Figma 747:4583) -->
  <div class="modal__panel modal__panel--detail" data-modal="detail" data-lenis-prevent>
    <button class="modal__close js-modal-close" type="button" aria-label="Закрити">
      <?php yka_icon( 'icons/close.svg' ); ?>
    </button>
    <div class="modal__body modal__body--center">
      <picture class="modal__image">
        <source srcset="<?php echo esc_url( yka_img( 'modals/detail-image-mob.webp' ) ); ?>" media="(max-width: 767.98px)" type="image/webp">
        <source srcset="<?php echo esc_url( yka_img( 'modals/detail-image.webp' ) ); ?>" type="image/webp">
        <source srcset="<?php echo esc_url( yka_img( 'modals/detail-image-mob.jpg' ) ); ?>" media="(max-width: 767.98px)">
        <img src="<?php echo esc_url( yka_img( 'modals/detail-image.jpg' ) ); ?>" alt="" width="1218" height="672" loading="lazy" decoding="async">
      </picture>
      <h2 class="modal__title h4 clr-black js-detail-title">Контрактне виробництво кормів</h2>
      <p class="modal__text text-lg clr-gray js-detail-text">Створюємо комбікорми під вашим брендом або рецептурою, гарантуючи точність виробництва, контроль якості та своєчасне виконання замовлень.</p>
      <a href="direction-detail.php" class="btn modal__cta js-detail-cta">
        <span class="btn__icon"><?php yka_icon( 'icons/arrow-diagonal.svg' ); ?></span>
        Детальніше про напрям
      </a>
    </div>
  </div>

  <!-- Учасник команди (about) -->
  <div class="modal__panel modal__panel--team" data-modal="team-igor" data-lenis-prevent>
    <button class="modal__close js-modal-close" type="button" aria-label="Закрити">
      <?php yka_icon( 'icons/close.svg' ); ?>
    </button>
    <div class="modal__team">
      <picture class="modal__team-photo">
        <img src="<?php echo esc_url( yka_img( 'about/team-igor.png' ) ); ?>" alt="Ігор П., головний технолог виробництва" width="633" height="635" loading="lazy" decoding="async">
      </picture>

      <div class="modal__team-body">
        <div class="modal__team-content">
          <div class="modal__team-head">
            <h2 class="modal__team-name h4 clr-black">Ігор П.</h2>
            <p class="modal__team-role text-lg clr-text">Головний технолог виробництва</p>
          </div>
          <div class="modal__team-text text-lg clr-muted">
            <p>Головний технолог виробництва відповідає за стабільність рецептур, контроль якості сировини та дотримання технологічних процесів на кожному етапі створення комбікормів. Його робота допомагає підтримувати прогнозований результат для партнерів і власних виробничих напрямів компанії.</p>
            <p>Особливу увагу Ігор приділяє точності дозування, безпеці виробництва та впровадженню рішень, які підвищують ефективність роботи команди. Завдяки системному підходу технологічні процеси залишаються керованими навіть під час масштабування виробництва.</p>
          </div>
        </div>

        <div class="modal__team-socials" aria-label="Соціальні мережі">
          <a href="#" class="modal__team-social" aria-label="Instagram" target="_blank" rel="noopener noreferrer"><?php yka_icon( 'icons/instagram.svg' ); ?></a>
          <a href="#" class="modal__team-social" aria-label="Facebook" target="_blank" rel="noopener noreferrer"><?php yka_icon( 'icons/facebook.svg' ); ?></a>
          <a href="#" class="modal__team-social" aria-label="TikTok" target="_blank" rel="noopener noreferrer"><?php yka_icon( 'icons/tiktok.svg' ); ?></a>
          <a href="#" class="modal__team-social" aria-label="YouTube" target="_blank" rel="noopener noreferrer"><?php yka_icon( 'icons/youtube.svg' ); ?></a>
        </div>
      </div>
    </div>
  </div>

  <div class="modal__panel modal__panel--team" data-modal="team-bohdan" data-lenis-prevent>
    <button class="modal__close js-modal-close" type="button" aria-label="Закрити">
      <?php yka_icon( 'icons/close.svg' ); ?>
    </button>
    <div class="modal__team">
      <picture class="modal__team-photo">
        <img src="<?php echo esc_url( yka_img( 'about/team-bohdan.jpg' ) ); ?>" alt="Богдан І., керівник відділу логістики та автопарку" width="633" height="635" loading="lazy" decoding="async">
      </picture>

      <div class="modal__team-body">
        <div class="modal__team-content">
          <div class="modal__team-head">
            <h2 class="modal__team-name h4 clr-black">Богдан І.</h2>
            <p class="modal__team-role text-lg clr-text">Керівник відділу логістики та автопарку</p>
          </div>
          <div class="modal__team-text text-lg clr-muted">
            <p>Керівник відділу логістики та автопарку відповідає за ефективну організацію логістичних процесів, координацію перевезень і безперебійну роботу автопарку компанії. Завдяки професійному підходу до планування маршрутів, контролю транспорту та оптимізації логістики забезпечується своєчасна доставка продукції партнерам і клієнтам.</p>
            <p>У своїй роботі він приділяє особливу увагу оперативності, безпеці перевезень та ефективному використанню ресурсів. Постійний контроль якості логістичних процесів і злагоджена взаємодія з усіма підрозділами компанії дозволяють підтримувати високий рівень сервісу та виконувати поставлені завдання у визначені терміни.</p>
          </div>
        </div>

        <div class="modal__team-socials" aria-label="Соціальні мережі">
          <a href="#" class="modal__team-social" aria-label="Instagram" target="_blank" rel="noopener noreferrer"><?php yka_icon( 'icons/instagram.svg' ); ?></a>
          <a href="#" class="modal__team-social" aria-label="Facebook" target="_blank" rel="noopener noreferrer"><?php yka_icon( 'icons/facebook.svg' ); ?></a>
          <a href="#" class="modal__team-social" aria-label="TikTok" target="_blank" rel="noopener noreferrer"><?php yka_icon( 'icons/tiktok.svg' ); ?></a>
          <a href="#" class="modal__team-social" aria-label="YouTube" target="_blank" rel="noopener noreferrer"><?php yka_icon( 'icons/youtube.svg' ); ?></a>
        </div>
      </div>
    </div>
  </div>

  <div class="modal__panel modal__panel--team" data-modal="team-khrystyna" data-lenis-prevent>
    <button class="modal__close js-modal-close" type="button" aria-label="Закрити">
      <?php yka_icon( 'icons/close.svg' ); ?>
    </button>
    <div class="modal__team">
      <picture class="modal__team-photo">
        <img src="<?php echo esc_url( yka_img( 'about/team-khrystyna.jpg' ) ); ?>" alt="Христина Б., ветеринарний лікар-консультант" width="633" height="635" loading="lazy" decoding="async">
      </picture>

      <div class="modal__team-body">
        <div class="modal__team-content">
          <div class="modal__team-head">
            <h2 class="modal__team-name h4 clr-black">Христина Б.</h2>
            <p class="modal__team-role text-lg clr-text">Ветеринарний лікар-консультант</p>
          </div>
          <div class="modal__team-text text-lg clr-muted">
            <p>Ветеринарний лікар-консультант супроводжує питання здоров'я птиці, профілактики ризиків і коректного використання кормових рішень у господарствах. Христина допомагає партнерам швидко знаходити практичні відповіді та підтримувати стабільні виробничі показники.</p>
            <p>У щоденній роботі вона поєднує ветеринарну експертизу з розумінням виробничих процесів. Це дозволяє бачити ситуацію комплексно: від умов утримання та годівлі до рекомендацій, які допомагають команді й клієнтам діяти впевнено.</p>
          </div>
        </div>

        <div class="modal__team-socials" aria-label="Соціальні мережі">
          <a href="#" class="modal__team-social" aria-label="Instagram" target="_blank" rel="noopener noreferrer"><?php yka_icon( 'icons/instagram.svg' ); ?></a>
          <a href="#" class="modal__team-social" aria-label="Facebook" target="_blank" rel="noopener noreferrer"><?php yka_icon( 'icons/facebook.svg' ); ?></a>
          <a href="#" class="modal__team-social" aria-label="TikTok" target="_blank" rel="noopener noreferrer"><?php yka_icon( 'icons/tiktok.svg' ); ?></a>
          <a href="#" class="modal__team-social" aria-label="YouTube" target="_blank" rel="noopener noreferrer"><?php yka_icon( 'icons/youtube.svg' ); ?></a>
        </div>
      </div>
    </div>
  </div>

  <!-- Відео (about) — джерело підставляє app-about.js при відкритті -->
  <div class="modal__panel modal__panel--video" data-modal="video" data-lenis-prevent>
    <button class="modal__close js-modal-close" type="button" aria-label="Закрити">
      <?php yka_icon( 'icons/close.svg' ); ?>
    </button>
    <div class="modal__video">
      <video class="js-modal-video" controls playsinline webkit-playsinline preload="none"></video>
    </div>
  </div>

  <!-- Дякуємо -->
  <div class="modal__panel modal__panel--thanks" data-modal="thanks" data-lenis-prevent>
    <button class="modal__close js-modal-close" type="button" aria-label="Закрити">
      <?php yka_icon( 'icons/close.svg' ); ?>
    </button>
    <div class="modal__body modal__body--center">
      <span class="modal__icon"><?php yka_icon( 'icons/modal-thanks.svg' ); ?></span>
      <h2 class="modal__title h4 clr-black">Ваше повідомлення успішно надіслано</h2>
      <p class="modal__text text-lg clr-gray">Дякуємо! Найближчим часом ми зв'яжемося з вами для уточнення деталей.</p>
    </div>
  </div>
</div>

<!-- Cookie popup — bottom-left, no overlay -->
<div class="popup popup--cookie js-popup" data-popup="cookie">
  <span class="popup__cookie-icon icon" aria-hidden="true"><?php yka_icon( 'icons/cookie.svg' ); ?></span>
  <button class="popup__close js-popup-close" type="button" aria-label="Закрити">
    <?php yka_icon( 'icons/close.svg' ); ?>
  </button>
  <div class="popup__content">
    <p class="popup__title h6 clr-black">Ми використовуємо файли cookie</p>
    <p class="popup__text text-sm clr-gray">Ми використовуємо <a href="privacy.php">cookies</a>, щоб зробити ваш досвід на сайті зручнішим та приємнішим. Натискаючи «Прийняти», ви погоджуєтеся на використання cookies відповідно до нашої <a href="privacy.php">Політики конфіденційності.</a></p>
    <div class="popup__actions">
      <button class="link-more popup__accept js-popup-close" type="button">
        Прийняти
        <span class="link-more__icon"><?php yka_icon( 'icons/arrow-diagonal.svg' ); ?></span>
      </button>
    </div>
  </div>
</div>
