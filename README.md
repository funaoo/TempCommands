# TempPerms
- El plugin TempPermissions permite crear vouchers en forma de papel que otorgan permisos temporales o permanentes a los jugadores.
- Usas el comando /tc seguido del nombre del permiso y opcionalmente el tiempo de duración. Si no especificas tiempo, el permiso será permanente. Los formatos de tiempo son: 30s para segundos, 15m para minutos, 2h para horas, 7d para días.
- Cuando ejecutas el comando, recibes un papel con el nombre del voucher en color morado, que muestra el permiso y la duración en su descripción. Al hacer click derecho con el papel, el permiso se activa automáticamente para el jugador que lo usa.
- Los permisos se guardan en una base de datos SQLite, por lo que persisten entre reinicios del servidor. Los permisos temporales expiran automáticamente cuando se cumple el tiempo, y hay un sistema que verifica cada segundo si algún permiso ha expirado para removerlo.
- Es útil para crear sistemas de recompensas, ventas de permisos VIP temporales, eventos especiales o cualquier situación donde necesites dar permisos de forma controlada y temporal a los jugadores.
