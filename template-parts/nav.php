<header>

  <!-- NAV -->
  <div class="nav">
    <div class="uk-background-primary">
      <nav class="uk-navbar-container uk-light uk-navbar-transparent uk-padding-small uk-padding-remove-vertical"
        uk-navbar>
        <div class="uk-navbar-left">

          <a class="uk-navbar-item uk-logo" href="<?php echo get_home_url(); ?>">
            [LOGO]
          </a>

          <ul class="uk-navbar-nav uk-visible@m">
            <?php $mainMenu = MenuHandler::getMenuItems('main'); ?>

            <?php foreach($mainMenu as $item) { ?>
            <li <?php echo $item['active'] ? 'class="uk-active"' : '' ?>>
              <a href="<?php echo $item['url']; ?>"><?php echo $item['title']; ?>
                <?php if(!empty($item['children'])) { ?><span class="uk-icon uk-margin-small-left"
                  uk-icon="icon: chevron-down"></span><?php } ?>
              </a>
              <?php if(!empty($item['children'])) { ?>
              <div class="uk-navbar-dropdown uk-background-dark">
                <ul class="uk-nav uk-navbar-dropdown-nav">
                  <?php foreach($item['children'] as $child) { ?>
                  <li <?php echo $item['active'] ? 'class="uk-active"' : '' ?>><a
                      href="<?php echo $child['url']; ?>"><?php echo $child['title']; ?></a>
                  </li>
                  <?php } ?>
                </ul>
              </div>
              <?php } ?>
            </li>
            <?php } ?>
          </ul>
        </div>
        <div class="uk-navbar-right">
          <a class="uk-navbar-toggle uk-navbar-item uk-hidden@m" data-uk-toggle data-uk-navbar-toggle-icon
            href="#offcanvas-nav"></a>
        </div>
      </nav>
    </div>
  </div>
  <!-- /NAV -->

  <!-- OFFCANVAS -->
  <div id="offcanvas-nav" data-uk-offcanvas="flip: true; overlay: false">
    <div class="uk-offcanvas-bar uk-offcanvas-bar-animation uk-offcanvas-slide">
      <button class="uk-offcanvas-close uk-close uk-icon" type="button" data-uk-close></button>
      <ul class="uk-nav uk-nav-default">
        <?php foreach($mainMenu as $item) { ?>
        <li
          class="<?php echo $item['active'] ? 'uk-active' : ''; ?> <?php echo empty($item['children']) ? 'uk-parent' : ''; ?>">
          <a href="<?php echo $item['url']; ?>"><?php echo $item['title']; ?>
            <?php if(!empty($item['children'])) { ?><span class="uk-icon uk-margin-small-left"
              uk-icon="icon: chevron-down"></span><?php } ?>
          </a>
          <?php if(!empty($item['children'])) { ?>
          <ul class="uk-nav-sub">
            <?php foreach($item['children'] as $child) { ?>
            <li <?php echo $item['active'] ? 'class="uk-active"' : '' ?>><a
                href="<?php echo $child['url']; ?>"><?php echo $child['title']; ?></a>
              <?php } ?>
          </ul>
          <?php } ?>
        </li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <!-- /OFFCANVAS -->

</header>