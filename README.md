pagantis-magento
================

Módulo de Magento para la versión Magento Community Edition v 1.8.1.x para pagantis.com

## Instrucciones de Instalación

1. Crea tu cuenta en Pagantis.com si aún no la tienes [desde aquí](https://bo.pagantis.com/users/sign_up)
2. Descarga el módulo de [aquí](https://github.com/pagantis/pagantis-magento/releases)
3. Instala el módulo en tu magento.
4. Configuralo con la información de tu cuenta que encontrarás en [el panel de gestión de Pagantis](https://bo.pagantis.com/api). Ten en cuenta que para hacer cobros reales deberás activar tu cuenta de Pagantis.
5. MUY IMPORTANTE: Añade en [la sección de notificaciones HTTP](https://bo.pagantis.com/notifications) de Pagantis la URL de notificación de tu tienda. Puedes ver más abajo instrucciones para saber la URL de tu comercio.


## URLs de notificación

Para que los pedidos se completen y los pedidos cobrados a través de tu cuenta de Pagantis aparezcan como pagados, debes indicarnos una URL de notificación. Esta URL es específica de tu tienda, pero muy sencilla de calcular: sólo tienes que añadir "/modules/tpvpagantis/validation.php" a la URL de inicio de tu tienda.

Ejemplos:

- Si tu tienda está en el dominio http://www.mitienda.es, la URL será: http://www.mitienda.es/index.php/pagantis_directpayment/payment/response
- Si tu tienda está en el subdominio http://shop.midominio.com, la URL será: http://shop.mitienda.es/index.php/pagantis_directpayment/payment/response
- Si tu tienda está en la carpeta "tienda" del dominio http://www.midominio.com, la URL será: http://www.mitienda.es/tienda/index.php/pagantis_directpayment/payment/response


### Soporte

Si tienes alguna duda o pregunta no tienes más que escribirnos un email a [soporte.tpv@pagantis.com] o a través de Twitter a [@PagantisDev](https://twitter.com/PagantisDev)


### Release notes

#### 1.0.3

- Versión inicial
