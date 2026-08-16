<?php
// Shared responsive clip-path shapes (objectBoundingBox 0..1). Referenced from
// CSS via clip-path: url(#id). Included once per page.
?>
<svg class="svg-defs" width="0" height="0" aria-hidden="true" focusable="false">
  <defs>
    <!-- Directions block: angled top + rounded corners -->
    <clipPath id="directions-clip" clipPathUnits="objectBoundingBox">
      <path d="M0.02,0 L0.985,0.05 Q1,0.052 1,0.09 L1,0.965 Q1,1 0.982,1 L0.018,1 Q0,1 0,0.965 L0,0.035 Q0,0 0.02,0 Z" />
    </clipPath>

    <!-- Directions block on mobile: viewport-edge shape with rounded corners -->
    <clipPath id="directions-mobile-clip" clipPathUnits="objectBoundingBox">
      <path d="M0.045,0.018 L0.955,0.040 C0.982,0.041 1,0.066 1,0.095 L1,0.955 C1,0.982 0.982,1 0.955,1 L0.045,1 C0.018,1 0,0.982 0,0.955 L0,0.065 C0,0.037 0.018,0.017 0.045,0.018 Z" />
    </clipPath>

    <!-- Direction tile (directions page, Figma 860x513): left edge leans in
         ~22px top → bottom, radius 24. Desktop/tablet keep a fixed aspect
         ratio, so objectBoundingBox radius stays stable. Mobile uses CSS
         border-radius + polygon instead (see _directions.scss). -->
    <clipPath id="direction-tile-clip" clipPathUnits="objectBoundingBox">
      <path d="M0,0.04678 C0,0.02095 0.01250,0 0.02791,0 H0.97209 C0.98750,0 1,0.02095 1,0.04678 V0.95322 C1,0.97417 0.98750,1 0.97209,1 H0.05349 C0.04099,1 0.02558,0.97417 0.02558,0.95322 L0,0.04678 Z" />
    </clipPath>

    <!-- Direction card: rounded, bottom-left inset (Figma 726x513) -->
    <clipPath id="direction-card-clip" clipPathUnits="objectBoundingBox">
      <path d="M0.00003,0.04888 C0,0.02224 0.01422,0 0.03306,0 H0.9668 C0.98506,0 0.99986,0.02094 1,0.04678 V0.95322 C1,0.97906 0.98506,1 0.9668,1 H0.06177 C0.04409,1 0.02954,0.98031 0.02875,0.95531 L0.00003,0.04888 Z" />
    </clipPath>

    <!-- Contact section background — broad soft angled panel -->
    <clipPath id="contact-panel-clip" clipPathUnits="objectBoundingBox">
      <path d="M0.018,0.050 L0.982,0.000 Q1,0 1,0.040 L1,0.965 Q1,1 0.982,1 L0.018,1 Q0,1 0,0.965 L0,0.085 Q0,0.053 0.018,0.050 Z" />
    </clipPath>

    <!-- Responsibility CTA panel — steeper slanted top edge (Figma 784:6206) -->
    <clipPath id="resp-cta-clip" clipPathUnits="objectBoundingBox">
      <path d="M0.02,0.125 L0.98,0.000 Q1,0 1,0.05 L1,0.95 Q1,1 0.98,1 L0.02,1 Q0,1 0,0.95 L0,0.175 Q0,0.13 0.02,0.125 Z" />
    </clipPath>

    <!-- Cooperation form card — gentle slanted top edge (Figma 833:6753) -->
    <clipPath id="coop-form-clip" clipPathUnits="objectBoundingBox">
      <path d="M0.018,0.10 L0.982,0.000 Q1,0 1,0.04 L1,0.96 Q1,1 0.982,1 L0.018,1 Q0,1 0,0.96 L0,0.14 Q0,0.10 0.018,0.10 Z" />
    </clipPath>

    <!-- Same cut, shallower — the form card is tall on mobile so the proportional
         slant must stay small to not crop the heading -->
    <clipPath id="coop-form-clip-mob" clipPathUnits="objectBoundingBox">
      <path d="M0.02,0.045 L0.98,0.000 Q1,0 1,0.025 L1,0.975 Q1,1 0.98,1 L0.02,1 Q0,1 0,0.975 L0,0.065 Q0,0.045 0.02,0.045 Z" />
    </clipPath>

    <!-- Contact card (home CTA) — soft angled top, rounded corners -->
    <clipPath id="contact-card-clip" clipPathUnits="objectBoundingBox">
      <path d="M0.032,0.058 L0.968,0.000 Q1,0 1,0.040 L1,0.965 Q1,1 0.968,1 L0.032,1 Q0,1 0,0.965 L0,0.098 Q0,0.061 0.032,0.058 Z" />
    </clipPath>

    <!-- Contact form card (Figma 706:5115, 731×643) — steeper top rise L→R -->
    <clipPath id="contact-form-card-clip" clipPathUnits="objectBoundingBox">
      <path d="M0,0.1419 C0,0.1254 0.011,0.1116 0.0254,0.1099 L0.9689,0.0002 C0.9855,0 1,0.0131 1,0.0321 V0.9679 C1,0.9856 0.9873,1 0.9717,1 H0.0283 C0.0127,1 0,0.9856 0,0.9679 V0.1419 Z" />
    </clipPath>

    <!-- Contacts address card (Figma 747:5906, 323×212) — left-edge bend like direction-card -->
    <clipPath id="contacts-card-clip" clipPathUnits="objectBoundingBox">
      <path d="M0.00008,0.1185 C-0.0019,0.054 0.0319,0 0.0743,0 H0.9257 C0.9667,0 1,0.0507 1,0.1132 V0.8868 C1,0.9493 0.9667,1 0.9257,1 H0.0982 C0.0585,1 0.0258,0.9525 0.024,0.8921 L0.00008,0.1185 Z" />
    </clipPath>

    <!-- Intro media — left: angled top (down-right), rounded -->
    <clipPath id="intro-left-clip" clipPathUnits="objectBoundingBox">
      <path d="M0.0296,0.00474 L0.9704,0.1553 Q1,0.16 1,0.19 L1,0.97 Q1,1 0.97,1 L0.03,1 Q0,1 0,0.97 L0,0.03 Q0,0 0.0296,0.00474 Z" />
    </clipPath>

    <!-- Intro media — right: right edge full, left dropped ~10%, rounded -->
    <clipPath id="intro-right-clip" clipPathUnits="objectBoundingBox">
      <path d="M0.0299,0.097 L0.9701,0.00299 Q1,0 1,0.03 L1,0.97 Q1,1 0.97,1 L0.03,1 Q0,1 0,0.97 L0,0.13 Q0,0.1 0.0299,0.097 Z" />
    </clipPath>

    <!-- Gallery: card left of the visual center (Figma 543x482) -->
    <clipPath id="gallery-left-clip" clipPathUnits="objectBoundingBox">
      <path d="M0,0.0498 C0,0.0216 0.0208,0 0.0458,0 L0.9574,0.0729 C0.9812,0.0738 1,0.0958 1,0.1226 L1,0.8757 C1,0.9025 0.9812,0.9245 0.9574,0.9255 L0.0458,0.9983 C0.0208,0.9993 0,0.9768 0,0.9485 L0,0.0498 Z" />
    </clipPath>

    <!-- Gallery: card right of the visual center (Figma 543x482) -->
    <clipPath id="gallery-right-clip" clipPathUnits="objectBoundingBox">
      <path d="M0,0.1226 C0,0.0958 0.0188,0.0738 0.0426,0.0729 L0.9542,0 C0.9792,0 1,0.0216 1,0.0498 L1,0.9485 C1,0.9768 0.9792,0.9993 0.9542,0.9983 L0.0426,0.9255 C0.0188,0.9245 0,0.9025 0,0.8757 L0,0.1226 Z" />
    </clipPath>

    <!-- Gallery: large edge card, visible from the left side of the viewport -->
    <clipPath id="gallery-edge-left-clip" clipPathUnits="objectBoundingBox">
      <path d="M0,0.057 C0.12,0.030 0.315,0.010 0.515,0.004 L0.958,0.037 C0.982,0.039 1,0.061 1,0.087 L1,0.913 C1,0.939 0.982,0.961 0.958,0.963 L0.515,0.996 C0.315,0.990 0.12,0.970 0,0.943 L0,0.057 Z" />
    </clipPath>

    <!-- Gallery: large edge card, visible from the right side of the viewport -->
    <clipPath id="gallery-edge-right-clip" clipPathUnits="objectBoundingBox">
      <path d="M0,0.087 C0,0.061 0.018,0.039 0.042,0.037 L0.485,0.004 C0.685,0.010 0.88,0.030 1,0.057 L1,0.943 C0.88,0.970 0.685,0.990 0.485,0.996 L0.042,0.963 C0.018,0.961 0,0.939 0,0.913 L0,0.087 Z" />
    </clipPath>

    <!-- News photo — image on the RIGHT: right edge full height, top slopes up
         to the right so the left side is shorter (cut on top) -->
    <clipPath id="news-top-clip" clipPathUnits="objectBoundingBox">
      <path d="M0.03,0.115 L0.97,0.004 Q1,0 1,0.035 L1,0.965 Q1,1 0.97,1 L0.03,1 Q0,1 0,0.965 L0,0.15 Q0,0.115 0.03,0.115 Z" />
    </clipPath>

    <!-- News photo — image on the LEFT: left edge full height, bottom slopes up
         to the right so the right side is shorter (cut on bottom) -->
    <clipPath id="news-bottom-clip" clipPathUnits="objectBoundingBox">
      <path d="M0.03,0 L0.97,0 Q1,0 1,0.035 L1,0.845 Q1,0.88 0.968,0.884 L0.032,0.996 Q0,1 0,0.965 L0,0.035 Q0,0 0.03,0 Z" />
    </clipPath>

    <!-- Modal panel — angled top with rounded start/end, shared by all modals -->
    <clipPath id="modal-panel-clip" clipPathUnits="objectBoundingBox">
      <path d="M0.0175,0.1068 L0.9738,0.0036 C0.9942,0.0036 1,0.0182 1,0.0461 L1,0.9709 C1,0.9869 0.9924,1 0.9825,1 L0.0175,1 C0.0076,1 0,0.9869 0,0.9709 L0,0.1384 C0,0.1214 0.0073,0.1080 0.0175,0.1068 Z" />
    </clipPath>

    <!-- Modal panel on mobile — same angled top, but with softer rounded corners -->
    <clipPath id="modal-panel-mobile-clip" clipPathUnits="objectBoundingBox">
      <path d="M0.060,0.040 L0.920,0.010 C0.970,0.008 0.998,0.030 1,0.075 L1,0.950 C1,0.980 0.980,1 0.950,1 L0.050,1 C0.020,1 0,0.980 0,0.950 L0,0.095 C0,0.062 0.024,0.043 0.060,0.040 Z" />
    </clipPath>

    <!-- Page hero title panel — angled top with rounded corners -->
    <clipPath id="page-hero-title-clip" clipPathUnits="objectBoundingBox">
      <path d="M0.030,0.140 L0.970,0.010 Q1,0.006 1,0.080 L1,0.940 Q1,1 0.970,1 L0.030,1 Q0,1 0,0.940 L0,0.210 Q0,0.145 0.030,0.140 Z" />
    </clipPath>

    <!-- Page hero title panel on mobile — smaller slope, same soft corners -->
    <clipPath id="page-hero-title-mobile-clip" clipPathUnits="objectBoundingBox">
      <path d="M0.050,0.105 L0.950,0.020 C0.980,0.017 1,0.040 1,0.090 L1,0.930 C1,0.975 0.975,1 0.930,1 L0.070,1 C0.025,1 0,0.975 0,0.930 L0,0.160 C0,0.125 0.020,0.108 0.050,0.105 Z" />
    </clipPath>

    <!-- Contacts map frame — top edge rises to the right (Figma 747:5901,
         1048x631: top-left drops ~81px, top-right flush), radius 24 -->
    <clipPath id="contacts-map-clip" clipPathUnits="objectBoundingBox">
      <path d="M0.0229,0.1254 L0.9771,0.0029 Q1,0 1,0.038 L1,0.962 Q1,1 0.9771,1 L0.0229,1 Q0,1 0,0.962 L0,0.1634 Q0,0.129 0.0229,0.1254 Z" />
    </clipPath>

    <!-- Contacts map on mobile — same rise, flattened for the taller ratio -->
    <clipPath id="contacts-map-clip-mob" clipPathUnits="objectBoundingBox">
      <path d="M0.045,0.052 L0.955,0.008 Q1,0.006 1,0.05 L1,0.95 Q1,1 0.955,1 L0.045,1 Q0,1 0,0.95 L0,0.096 Q0,0.056 0.045,0.052 Z" />
    </clipPath>

    <!-- Article author panel — angled top cut (Figma 784:8193), rounded corners -->
    <clipPath id="article-author-clip" clipPathUnits="objectBoundingBox">
      <path d="M0,0.1355 C0,0.1178 0.0079,0.1032 0.0181,0.1021 L0.9796,0 C0.9906,0 1,0.0142 1,0.0335 V0.9662 C1,0.9846 0.9914,1 0.9807,1 H0.0193 C0.0086,1 0,0.9846 0,0.9662 V0.1355 Z" />
    </clipPath>

    <!-- Article author on mobile — flatter ~80° top cut, corner radius ≈ bottom -->
    <clipPath id="article-author-clip-mob" clipPathUnits="objectBoundingBox">
      <path d="M0.055,0.055 L0.945,0.006 C0.978,0.004 1,0.024 1,0.055 L1,0.945 C1,0.976 0.976,1 0.945,1 L0.055,1 C0.024,1 0,0.976 0,0.945 L0,0.095 C0,0.068 0.022,0.057 0.055,0.055 Z" />
    </clipPath>

    <!-- Production step card (Figma 899:5972, 724×332) — slanted LEFT edge:
         the card is a touch wider at the top, 24 radius on every corner -->
    <clipPath id="production-card-clip" clipPathUnits="objectBoundingBox">
      <path d="M0.0331,0 L0.9669,0 Q1,0 1,0.0723 L1,0.9277 Q1,1 0.9669,1 L0.0587,1 Q0.0256,1 0.0256,0.9277 L0,0.0723 Q0,0 0.0331,0 Z" />
    </clipPath>

    <!-- Product detail gallery (Figma 1250:7147, 828×655) — rounded photo with
         a 135×136 notch cut out of the top-right corner for the rooster badge.
         The gallery keeps the 828/655 ratio at every breakpoint, so the notch
         and its 24px radii scale proportionally. -->
    <clipPath id="product-gallery-clip" clipPathUnits="objectBoundingBox">
      <path d="M0.029,0 L0.808,0 Q0.837,0 0.837,0.0366 L0.837,0.171 Q0.837,0.2076 0.866,0.2076 L0.971,0.2076 Q1,0.2076 1,0.2442 L1,0.9634 Q1,1 0.971,1 L0.029,1 Q0,1 0,0.9634 L0,0.0366 Q0,0 0.029,0 Z" />
    </clipPath>

    <!-- Same cut on the shorter mobile card — shallower so it never eats text -->
    <clipPath id="production-card-clip-mob" clipPathUnits="objectBoundingBox">
      <path d="M0.055,0 L0.945,0 Q1,0 1,0.1 L1,0.9 Q1,1 0.945,1 L0.073,1 Q0.018,1 0.018,0.9 L0,0.1 Q0,0 0.055,0 Z" />
    </clipPath>
  </defs>
</svg>
