import re

with open('page.php') as f:
    page = f.read()
for pair in (
    ("/**\n", "/**\nTemplate Name: Wide Template\n"),
    ("<?php get_sidebar( 'content-bottom' ); ?>", ""),
    ("<?php get_sidebar(); ?>", ""),
    ("get_header()", "get_header( 'wide' )"),
):
    page = page.replace(*pair)
with open('page-wide.php', 'w') as f:
    f.write(page)
for pair in (
    ("Template Name: Wide Template", "Template Name: Content Only Template"),
    ("get_header( 'wide' )", "get_header( 'content-only' )"),
    ("get_footer()", "get_footer( 'content-only' )"),
):
    page = page.replace(*pair)
with open('page-content-only.php', 'w') as f:
    f.write(page)

with open('header.php') as f:
    header = f.read()
header = header.replace("body_class()", "body_class( 'no-sidebar' )")
with open('header-wide.php', 'w') as f:
    f.write(header)
header = header.replace("body_class( 'no-sidebar' )", "body_class([ 'no-sidebar', 'content-only' ])")
header = re.sub('<header id="masthead" class="site-header">.*</header>',
                '<header id="masthead" class="site-header"></header>', header, flags=re.DOTALL)
with open('header-content-only.php', 'w') as f:
    f.write(header)

with open('footer.php') as f:
    footer = f.read()
footer = re.sub('<footer id="colophon" class="site-footer">.*</footer>',
                '<footer id="colophon" class="site-footer"></footer>', footer, flags=re.DOTALL)
with open('footer-content-only.php', 'w') as f:
    f.write(footer)
