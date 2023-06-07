<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Ticketreservierung</title>
    <style>
/* Style.css */

/* Global Styles */
img.wp-smiley,
img.emoji {
	display: inline !important;
	border: none !important;
	box-shadow: none !important;
	height: 1em !important;
	width: 1em !important;
	margin: 0 0.07em !important;
	vertical-align: -0.1em !important;
	background: none !important;
	padding: 0 !important;
}

body {
	color: #50545C;
	font-weight: 400;
	line-height: 1.55;
	letter-spacing: 0px;
	background-color: #e0e0e0;
}

a,
a:hover {
	color: #f7ad00;
	text-decoration: none;
}

h1,
h2,
h3,
h4,
h5,
h6 {
	font-weight: 500;
	line-height: 1.1;
	letter-spacing: 0px;
}

h1 {
	color: #63007f;
}

h2 {
	color: #63007f;
}

h3.widget-title,
.entry-content h3.widget-title {
	color: #63007f;
	font-size: 22px;
	margin-bottom: 10px;
}

/* Header
.site-header {
	background: url("cropped-Beitrag_2.jpg") no-repeat center top;
	background-attachment: fixed;
	background-size: cover;
}
 */
.site-title a {
	font-size: 18px;
	color: #f9f9f9;
}

.site-description {
	color: #f9f9f9;
}

/* Navigation */
.main-navigation li {
	font-size: 16px;
}

/* Logo */
.site-branding {
	padding: 225px 0;
}

@media only screen and (max-width: 1024px) {
	.site-branding {
		padding: 100px 0;
	}
}

.site-logo,
.woocommerce .site-logo,
.woocommerce-page .site-logo {
	max-width: 200px;
}

/* Colors */
.entry-meta a:hover,
.entry-title a:hover,
.widget-area a:hover,
.social-navigation li a:hover,
a,
.featured-article-content .entry-title span {
	color: #63007f;
}

.read-more,
.nav-previous:hover,
.nav-next:hover,
button,
.button,
input[type="button"],
input[type="reset"],
input[type="submit"] {
	background-color: #63007f;
}

.entry-thumb:after {
	background-color: rgba(99, 0, 127, 0.4);
}

/* WooCommerce */
.woocommerce ul.products li.product .button {
	background-color: #63007f;
}

.woocommerce ul.products li.product h2.woocommerce-loop-product__title:hover {
	color: #63007f;
}

.woocommerce ul.products li.product-category h2.woocommerce-loop-category__title:hover {
	color: #63007f;
}

.woocommerce ul.products li.product-category h2.woocommerce-loop-category__title:hover .count {
	color: #63007f;
}

.woocommerce div.product form.cart button.button {
	background-color: #63007f;
}

.woocommerce #reviews #comments ol.commentlist li div.star-rating {
	color: #63007f;
}

.woocommerce #review_form #respond .form-submit input[type="submit"] {
	background-color: #63007f;
}

.woocommerce div.product .woocommerce-tabs ul.tabs li.active {
	color: #63007f;
}

.single-product h2.related_products_title {
	color: #63007f;
}

.woocommerce-cart header.entry-header h1.entry-title {
	color: #63007f;
}

.woocommerce-cart input.button {
	background-color: #63007f;
}

.woocommerce-checkout input.button {
	background-color: #63007f;
}

.woocommerce-account header.entry-header h1.entry-title {
	color: #63007f;
}

.woocommerce-account .woocommerce-MyAccount-navigation ul li.is-active {
	color: #63007f;
}


.instagram-icon {
  display: inline-block;
  width: 16px;
  height: 16px;
  background-image: url('./ig-logo-bw.png'); /* Pfad zum Instagram-Logo */
  background-size: 16px 16px;
  background-repeat: no-repeat;
  background-position: center;
  margin-right: 5px;
  vertical-align: middle;
}
    </style>
<link rel="icon" href="./images.png">
</head>

<body>
    <div class="container">
        <h1>Hallo <?php echo $vorname; ?>,</h1>

        <p>Es wurde ein Ticket für Ihre E-Mail-Adresse reserviert. Bitte überprüfen Sie die folgenden Daten:</p>

        <ul>
            <li>Name: <?php echo $name; ?></li>
            <li>Vorname: <?php echo $vorname; ?></li>
            <li>Schule: <?php echo $schule; ?></li>
            <li>Geburtsdatum: <?php echo $gb_datum; ?></li>
        </ul>

        <p>Wenn die Daten korrekt sind, klicken Sie bitte auf den unten stehenden Button, um die Reservierung zu
            bestätigen:</p>

        <button type="button">Reservierung bestätigen</button>

        <p>Wenn die Daten nicht korrekt sind, klicken Sie hier, um sie anzupassen.</p>

        <p>Falls Sie die Reservierung stornieren wollen, drücken Sie bitte <a href="#">hier</a>.</p>

        <p>Bei Fragen oder Problemen schreiben Sie uns gerne bei <a href="https://www.instagram.com/aks.karlsruhe/"><span class="instagram-icon"></span>Instagram</a> oder schreiben Sie uns eine E-Mail.</p>

        <p>Vielen Dank!</p>
    </div>
</body>

</html>
