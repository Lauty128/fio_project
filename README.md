# GESTOR DE PROVEEDORES

Este proyecto busca impulsar la eficiencia en la gestión de proveedores de equipos médicos. En un entorno donde la atención médica y la tecnología convergen para mejorar la calidad del servicio, la optimización de los procesos de suministro y contacto con proveedores se vuelve esencial para garantizar la atención eficaz a los pacientes.

El objetivo principal de este proyecto es desarrollar un sistema de almacenamiento y gestión de proveedores de equipos médicos. Nuestro sistema tiene como propósito brindar una plataforma centralizada y fácil de usar que permita a las instituciones médicas identificar, contactar y filtrar proveedores de manera eficiente, según los tipos específicos de equipos médicos que ofrecen.


### Patrón MVC:

En nuestra API, seguimos el patrón de diseño Modelo-Vista-Controlador (MVC), que es una arquitectura de software que organiza la aplicación en tres componentes principales: el Modelo, la Vista y el Controlador.

1. **Modelo:**
   - *Definición:* El Modelo representa los datos y la lógica de negocio de la aplicación. En nuestra API, los modelos son responsables de interactuar con la base de datos y gestionar la manipulación y recuperación de datos.
   - *Implementación:* En los modelos, formulamos las consultas SQL y definimos la estructura de los datos. Aquí es donde se lleva a cabo la lógica que manipula y recupera la información necesaria.

2. **Vista (Aunque no se utilice directamente en una API sin vistas):**
   - *Definición:* La Vista maneja la presentación y visualización de los datos al usuario. En una API sin interfaz gráfica, este componente puede ser menos prominente, ya que la presentación se gestiona principalmente en el lado del cliente.
   - *Implementación:* Aunque no haya vistas directas en la API, los datos enviados a través de las respuestas de la API sirven como la "vista" en un sentido más amplio.

3. **Controlador:**
   - *Definición:* El Controlador actúa como intermediario entre el Modelo y la Vista. En nuestra API, los controladores contienen la lógica que gestiona las solicitudes entrantes, extrae y formatea los parámetros, y coordina la interacción entre los modelos y el cliente.
   - *Implementación:* En los controladores, se extraen los parámetros de las solicitudes, se validan y formatean según sea necesario. Aquí es donde reside la lógica principal de la API, y es común la reutilización de modelos en diferentes controladores.

### Relación entre Controladores y Modelos:

En algunos casos, un controlador puede necesitar interactuar con múltiples modelos para realizar operaciones específicas. Esto es una práctica común cuando se requiere información de diferentes fuentes o se realizan operaciones más complejas.

1. **Reutilización de Modelos:**
   - Algunos controladores pueden reutilizar lógica definida en otros modelos. Esto promueve la reutilización de código y asegura la coherencia en la manipulación de datos.

2. **Uso de Múltiples Modelos en un Controlador:**
   - En determinadas situaciones, un controlador puede interactuar con dos o más modelos para realizar operaciones más complejas o para acceder a diferentes conjuntos de datos.
