<?php
// Decorative orange line that draws itself down the page on scroll (Figma).
//
// Vars:
//   $scroll_line_class  — extra class on the wrapper
//   $scroll_line_static — true: render the same vector as a plain watermark
//                         (no ids, no js hooks, no draw animation). Used inside
//                         overlays that need the decor but sit outside <main>,
//                         e.g. the mobile catalog filter sheet.
// Values arrive through get_template_part( …, [ 'class' => …, 'static' => … ] ).
$scroll_line_class = isset( $args['class'] ) ? trim( (string) $args['class'] ) : '';
$scroll_line_static = ! empty( $args['static'] );
$scroll_line_d = "M782.787 25.0069C678.287 337.507 853.789 375.007 1465.79 423.507C1853.51 454.233 1698.29 930.507 994.289 1427.01C764.081 1589.36 743.905 2020.91 806.787 2350.01C875.286 2708.51 1519.29 2970.05 1679.29 3519.05C1807.29 3958.25 1017.46 4557.06 676.445 4735.5C294.886 4935.15 -17.2852 5249.39 46.0268 5517.97C155.692 5983.2 812.054 5541.34 1062.93 5704.88C1213.56 5803.08 1093.09 6018.19 1161.23 6092.18C1215.05 6150.61 1352.02 6173.95 1418.21 6109.7C1487.36 6042.57 1404.31 5968 1342.29 6016.24C1272.2 6070.76 1263.44 6238.2 1350.47 6289.84C1471.45 6361.64 1610.95 6200.23 1716.08 6308.29C1858.99 6455.19 1681.29 6875.27 1498.87 6850.94C1228.87 6814.94 843.261 6865.44 792.368 7007.94C762.368 7091.94 938.85 7132.53 1011.87 7171.94C1395.37 7378.94 919.496 7478.87 602.868 7668.44C367.367 7809.44 661.367 7994.94 849.867 7919.94C1038.37 7844.94 1956.37 7516.44 1839.37 8146.44C1783.1 8449.44 1180.37 8383.94 746.867 8501.94C613.534 8516.44 244.867 8797.44 158.368 8906.44C66.0423 9022.79 -67.1324 9401.94 117.868 9492.94C302.868 9583.94 733.013 9769.67 790.867 10233.9C891.367 11040.4 -695.633 12083.9 1253.87 12127.9";
?>
<div class="scroll-line<?php echo $scroll_line_static ? ' scroll-line--static' : ''; ?><?php echo $scroll_line_class ? ' ' . htmlspecialchars($scroll_line_class, ENT_QUOTES, 'UTF-8') : ''; ?>" aria-hidden="true">
  <svg class="scroll-line__svg" viewBox="0 0 1838 12153" fill="none" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
    <?php if ($scroll_line_static): ?>
      <!-- watermark only: no id/use pair, so it can never collide with the page line -->
      <path class="scroll-line__static" d="<?php echo $scroll_line_d; ?>"
        stroke="var(--scroll-line-static-stroke, rgba(235, 110, 1, 0.03))" stroke-width="50" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
    <?php else: ?>
      <defs>
        <path id="scroll-line-path" class="js-scroll-line-measure" d="<?php echo $scroll_line_d; ?>" />
      </defs>

      <use class="scroll-line__base" href="#scroll-line-path" xlink:href="#scroll-line-path"
        stroke="var(--scroll-line-base-stroke, #EB6E01)" stroke-opacity="var(--scroll-line-base-opacity, 0)" stroke-width="50" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
      <use class="scroll-line__draw js-scroll-line" href="#scroll-line-path" xlink:href="#scroll-line-path"
        stroke="rgba(235, 110, 1, 0.03)" stroke-opacity="var(--scroll-line-draw-opacity, 1)" stroke-width="50" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"
        stroke-dasharray="99999" stroke-dashoffset="99999" />
    <?php endif; ?>
  </svg>
</div>
<?php unset($scroll_line_static, $scroll_line_d); ?>
