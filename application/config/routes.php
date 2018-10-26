<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

/* Vistas de inicio de sesión */
$route['login'] = 'CLogin/login/';
$route['logout'] = 'CLogin/logout/';
$route['home'] = 'Home/home/';
$route['admin'] = 'Welcome/admin/';

/* Perfiles */
$route['profile'] = 'CPerfil';
$route['profile/register'] = 'CPerfil/register';
$route['profile/edit/(:num)'] = 'CPerfil/edit/$1';
$route['profile/delete/(:num)'] = 'CPerfil/delete/$1';

/*   Users */
$route['users'] = 'CUser';
$route['users/register'] = 'CUser/register';
$route['users/edit/(:num)'] = 'CUser/edit/$1';
$route['users/change_passwd'] = 'CChangePasswd/index';
$route['users/update_passwd'] = 'CChangePasswd/update_passwd';
$route['users/update_session'] = 'CUser/transcurrido';

/*   Menús */
$route['menus'] = 'CMenus';
$route['menus/register'] = 'CMenus/register';
$route['menus/edit/(:num)'] = 'CMenus/edit/$1';
$route['menus/delete/(:num)'] = 'CMenus/delete/$1';

/*   Submenús */
$route['submenus'] = 'CSubMenus';
$route['submenus/register'] = 'CSubMenus/register';
$route['submenus/edit/(:num)'] = 'CSubMenus/edit/$1';
$route['submenus/delete/(:num)'] = 'CSubMenus/delete/$1';

/*   Acciones */
$route['actions'] = 'CAcciones';
$route['actions/register'] = 'CAcciones/register';
$route['actions/edit/(:num)'] = 'CAcciones/edit/$1';
$route['actions/delete/(:num)'] = 'CAcciones/delete/$1';

/*   Monedas */
$route['coins'] = 'CCoins';
$route['coins/register'] = 'CCoins/register';
$route['coins/edit/(:num)'] = 'CCoins/edit/$1';
$route['coins/delete/(:num)'] = 'CCoins/delete/$1';

/* Orders */
$route['orders/(:num)'] = 'COrders/index/$1';
$route['orders/details/(:num)'] = 'COrders/details/$1';
$route['orders/details_orders/(:num)'] = 'COrders/details_orders/$1';
$route['orders/invoice/(:num)'] = 'COrders/pdf_invoice/$1';
$route['orders/order/(:num)'] = 'COrders/pdf_order/$1';
//$route['orders/order_cotization/(:num)'] = 'COrders/pdf_order_cotization/$1';
$route['orders/estimate/(:num)'] = 'COrders/pdf_order_cotization/$1';
$route['orders/payment/(:num)/(:num)'] = 'COrders/pdf_payment/$1/$2';
$route['orders/update_num_invoice'] = 'COrders/update_order';

/* Products */
$route['products/(:num)'] = 'CProducts/index/$1';
$route['products/catalogue/(:num)'] = 'CProducts/pdf_catalogue/$1';
$route['products/update/price'] = 'CProducts/update_price_prestashop';
$route['products/update/public_price'] = 'CProducts/update_public_price_prestashop';
$route['products/update/alternative_price'] = 'CProducts/update_alternative_price_prestashop';
$route['export_csv'] = 'CProducts/export_csv';

/* Precios */
$route['prices'] = 'CPrices';
$route['prices/save'] = 'CPrices/save_prices';
$route['prices/update'] = 'CPrices/update_prices';
$route['prices_list'] = 'CPricesList';
$route['prices/edit/(:num)'] = 'CPricesList/edit/$1';
$route['prices_json'] = 'CPrices/ajax_prices';

/* Costos Fijos */
$route['fixed_costs'] = 'CFixedCosts';

/* Costos Variables */
$route['variable_costs'] = 'CVariableCosts';

/* Materiales */
$route['materials'] = 'CMaterials';

/* Colores */
$route['colors'] = 'CColors';

/*assets*/
$route['assets/(:any)'] = 'assets/$1';
