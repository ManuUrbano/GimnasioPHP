<?php
session_start();
/**
 * Incluimos los modelos que necesite este controlador
 */
require_once MODELS_FOLDER . 'UserModel.php';

/**
 * Clase controlador que será la encargada de obtener, para cada tarea, los datos
 * necesarios de la base de datos, y posteriormente, tras su proceso de elaboración,
 * enviarlos a la vista para su visualización
 */
class UserController extends BaseController
{
   // El atributo $modelo es de la 'clase modelo' y será a través del que podremos 
   // acceder a los datos y las operaciones de la base de datos desde el controlador
   private $modelo;
   //$mensajes se utiliza para almacenar los mensajes generados en las tareas, 
   //que serán posteriormente transmitidos a la vista para su visualización
   private $mensajes;

   /**
    * Constructor que crea automáticamente un objeto modelo en el controlador e
    * inicializa los mensajes a vacío
    */
   public function __construct()
   {
      parent::__construct();
      $this->modelo = new UserModel();
      $this->mensajes = [];
   }

   public function inicio()
   {
      $parametros = [
         "tituloventana" => "Inicio del sitio Usuario"
      ];
      $this->view->show("paginaUsuario", $parametros);
   }

   /**
    * Método que obtiene de la base de datos el listado de usuarios y envía dicha
    * infomación a la vista correspondiente para su visualización
    */
   public function listado()
   {
      // Almacenamos en el array 'parametros[]'los valores que vamos a mostrar en la vista
      $parametros = [
         "tituloventana" => "Base de Datos con PHP y PDO",
         "datos" => NULL,
         "mensajes" => []
      ];
      // Realizamos la consulta y almacenamos los resultados en la variable $resultModelo
      $resultModelo = $this->modelo->listado();
      // Si la consulta se realizó correctamente transferimos los datos obtenidos
      // de la consulta del modelo ($resultModelo["datos"]) a nuestro array parámetros
      // ($parametros["datos"]), que será el que le pasaremos a la vista para visualizarlos
      if ($resultModelo["correcto"]) :
         $parametros["datos"] = $resultModelo["datos"];
         //Definimos el mensaje para el alert de la vista de que todo fue correctamente
         $this->mensajes[] = [
            "tipo" => "success",
            "mensaje" => "El listado se realizó correctamente"
         ];
      else :
         //Definimos el mensaje para el alert de la vista de que se produjeron errores al realizar el listado
         $this->mensajes[] = [
            "tipo" => "danger",
            "mensaje" => "El listado no pudo realizarse correctamente!! :( <br/>({$resultModelo["error"]})"
         ];
      endif;
      //Asignamos al campo 'mensajes' del array de parámetros el valor del atributo 
      //'mensaje', que recoge cómo finalizó la operación:
      $parametros["mensajes"] = $this->mensajes;
      // Incluimos la vista en la que visualizaremos los datos o un mensaje de error
      $this->view->show("ListadoUser", $parametros);
   }

   /**
    * Método de la clase controlador que realiza la eliminación de un usuario a 
    * través del campo id.
    */
   public function deluser()
   {
      // verificamos que hemos recibido los parámetros desde la vista de listado 
      if (isset($_GET['id']) && (is_numeric($_GET['id']))) {
         $id = $_GET["id"];
         //Realizamos la operación de suprimir el usuario con el id=$id
         $resultModelo = $this->modelo->deluser($id);

         if ($resultModelo["correcto"]) {
            $this->mensajes[] = [
               "tipo" => "success",
               "mensaje" => "Se eliminó correctamente el usuario $id"
            ];
         } else {
            $this->mensajes[] = [
               "tipo" => "danger",
               "mensaje" => "La eliminación del usuario no se realizó correctamente!! :( <br/>({$resultModelo["error"]})"
            ];
         }
      } else {
         $this->mensajes[] = [
            "tipo" => "danger",
            "mensaje" => "No se pudo acceder al id del usuario a eliminar!! :("
         ];
      }

      //Si la variable vista es 1, quiere decir que hemos llamando al metodo desde la vista de listar usuario y llamaremos al metodo
      //para mostrar esa vista cargando los datos.
      //Si no es asi, cargamos la vista de usuarios no validados mediante la funcion especifica.
      if (isset($_GET['vista']) == 1) {
         $this->listaUsuarios();
      } else {
         $this->listaUsuariosNoValidados();
      }
   }


   /**
    * Método de la clase controlador que realiza el REGISTRO en la bdbb de un usario CON TODOS LOS DATOS de la tabla usuarios
    * 
    */
   public function adduser()
   {
      $errores = array();

      // Array asociativo que almacenará los mensajes de error que se generen por cada campo

      // Si se ha pulsado el botón guardar...
      if (isset($_POST) && !empty($_POST) && isset($_POST['submit'])) { // y hemos recibido las variables del formulario y éstas no están vacías...
         $usuario = filter_var($_POST['txtusuario'], FILTER_SANITIZE_STRING);
         $password = $_POST['txtpassword']; //sha1();
         $email = filter_var($_POST['txtemail'], FILTER_SANITIZE_STRING);
         $nombre = filter_var($_POST['txtnombre'], FILTER_SANITIZE_STRING);
         $apellido1 = filter_var($_POST['txtapellido1'], FILTER_SANITIZE_STRING);
         $apellido2 = filter_var($_POST['txtapellido2'], FILTER_SANITIZE_STRING);
         $dni = filter_var($_POST['txtdni'], FILTER_SANITIZE_STRING);
         $direccion = filter_var($_POST['txtdireccion'], FILTER_SANITIZE_STRING);
         $telefono = filter_var($_POST['txttelefono'], FILTER_SANITIZE_STRING);


         //Si no se cumple la expresión regular se genera un error especifico.
         if (!preg_match("/^[a-z0-9ü_]{3,15}$/", $usuario)) {
            $this->mensajes[] = [
               "campo" => "usuario",
               "tipo" => "danger",
               "mensaje" => "Usuario no valido. Caracteres alfanumericos de 3 a 15 veces."
            ];
            $errores["usuario"] = "Error: No valido";
            $parametros = ["mensajes" => $this->mensajes];
         }
         //Si no se cumple la expresión regular se genera un error especifico.
         if (!preg_match("/^[^@]+@[^@]+\.[a-zA-Z]{2,}$/", $email)) {
            $this->mensajes[] = [
               "campo" => "email",
               "tipo" => "danger",
               "mensaje" => "Email no valido"
            ];
            $errores["email"] = "Error: No valido";
            $parametros = ["mensajes" => $this->mensajes];
         }

         //Si no se cumple la expresión regular se genera un error especifico.
         if (!preg_match("/^[a-zA-Z]{1,50}$/", $nombre)) {
            $this->mensajes[] = [
               "campo" => "nombre",
               "tipo" => "danger",
               "mensaje" => "Nombre no valido."
            ];
            $errores["nombre"] = "Error: No valido";
            $parametros = ["mensajes" => $this->mensajes];
         }
         //Si no se cumple la expresión regular se genera un error especifico.
         if (!preg_match("/^[a-zA-Z]{1,50}$/", $apellido1)) {
            $this->mensajes[] = [
               "campo" => "apellido1",
               "tipo" => "danger",
               "mensaje" => "Apellido 1 no valido."
            ];
            $errores["apellido1"] = "Error: No valido";
            $parametros = ["mensajes" => $this->mensajes];
         }
         //Si no se cumple la expresión regular se genera un error especifico.
         if (!preg_match("/^[a-zA-Z]{1,50}$/", $apellido2)) {
            $this->mensajes[] = [
               "campo" => "apellido2",
               "tipo" => "danger",
               "mensaje" => "Apellido 2 no valido."
            ];
            $errores["nombre"] = "Error: No valido";
            $parametros = ["mensajes" => $this->mensajes];
         }

         if (!preg_match("/^\d{8}[a-zA-Z]{1}$/", $dni)) {
            $this->mensajes[] = [
               "campo" => "dni",
               "tipo" => "danger",
               "mensaje" => "Dni no valido."
            ];
            $errores["nombre"] = "Error: No valido";
            $parametros = ["mensajes" => $this->mensajes];
         }

         if (!preg_match("/^[6-7]{1}[0-9]{8}$/", $telefono) && !preg_match("/^[8-9]{1}[0-9]{8}$/", $telefono)) {
            $this->mensajes[] = [
               "campo" => "telefono",
               "tipo" => "danger",
               "mensaje" => "Solo fijos nacionales o moviles."
            ];
            $errores["nombre"] = "Error: No valido";
            $parametros = ["mensajes" => $this->mensajes];
         }


         if (!preg_match("/[a-zA-Z0-9_]{1,100}/", $direccion)) {
            $this->mensajes[] = [
               "campo" => "direccion",
               "tipo" => "danger",
               "mensaje" => "Nombre no valido."
            ];
            $errores["nombre"] = "Error: No valido";
            $parametros = ["mensajes" => $this->mensajes];
         }
         if (count($errores) > 0) {
            $this->view->show("registroUsuarios", $parametros);
         }

         // Si no se han producido errores realizamos el registro del usuario
         if (count($errores) == 0) {

            //Definimos la variable ROL_ID para cuando se hace la insercion en la BBDD meta el usuario con el ROL 2
            $rol_id = 2;
            $resultModelo = $this->modelo->adduser([
               'usuario' => $usuario,
               "password" => $password,
               'nombre' => $nombre,
               'apellido1' => $apellido1,
               'apellido2' => $apellido2,
               'dni' => $dni,
               'email' => $email,
               'telefono' => $telefono,
               'direccion' =>$direccion,
               'rol_id' => $rol_id,
            ]);
            if ($resultModelo["correcto"]) {
               $this->mensajes[] = [
                  "tipo" => "success",
                  "mensaje" => "Registro completado con exito!"
               ];
            } else {
               //Retornamos errores a la vista principal.
               $this->view->show("registroUsuarios", $parametros);
            };
         }
      }

      //Si todo esta ok
      if (count($errores) == 0) {
         $_SESSION['usuario'] = $usuario;

         $this->mensajes[] = [
            "campo" => "nuevoUsuario",
            "tipo" => "warning",
            "mensaje" => "Un administrador validará tu perfil en un rango de 24/48 horas."
         ];
         $errores["nombre"] = "Error: No valido";
         $parametros = ["mensajes" => $this->mensajes];
         $_SESSION['perfilCompleto'] = true;
         $this->view->show("Inicio", $parametros);
      }
   }

   /**
    * Metodo para que el ADMIN pueda editar cualquier usuario
    */
   public function actuser()
   {
      // Array asociativo que almacenará los mensajes de error que se generen por cada campo
      $errores = array();
      // Si se ha pulsado el botón guardar...
      if (isset($_POST) && !empty($_POST) && isset($_POST['submit'])) { // y hemos recibido las variables del formulario y éstas no están vacías...
         $nombre = filter_var($_POST['txtnombre'], FILTER_SANITIZE_STRING);
         $apellido1 = filter_var($_POST['txtapellido1'], FILTER_SANITIZE_STRING); 
         $apellido2 = filter_var($_POST['txtapellido2'], FILTER_SANITIZE_STRING);
         $dni = filter_var($_POST['txtdni'], FILTER_SANITIZE_STRING);
         $direccion = filter_var($_POST['txtdireccion'], FILTER_SANITIZE_STRING);
         $telefono = filter_var($_POST['txttelefono'], FILTER_SANITIZE_STRING);
         $password = $_POST['txtpassword']; //sha1();

         $imagen = NULL;

         if (isset($_FILES["imagen"]) && (!empty($_FILES["imagen"]["tmp_name"]))) {
            // Verificamos la carga de la imagen
            // Comprobamos si existe el directorio fotos, y si no, lo creamos
            if (!is_dir("fotos")) {
               $dir = mkdir("fotos", 0777, true);
            } else {
               $dir = true;
            }
            // Ya verificado que la carpeta uploads existe movemos el fichero seleccionado a dicha carpeta
            if ($dir) {
               //Para asegurarnos que el nombre va a ser único...
               $nombrefichimg = time() . "-" . $_FILES["imagen"]["name"];
               // Movemos el fichero de la carpeta temportal a la nuestra
               $movfichimg = move_uploaded_file($_FILES["imagen"]["tmp_name"], "fotos/" . $nombrefichimg);
               $imagen = $nombrefichimg;
               // Verficamos que la carga se ha realizado correctamente
               if ($movfichimg) {
                  $imagencargada = true;
               } else {
                  $imagencargada = false;
                  $this->mensajes[] = [
                     "tipo" => "danger",
                     "mensaje" => "Error: La imagen no se cargó correctamente!"
                  ];
                  $errores["imagen"] = "Error: La imagen no se cargó correctamente!";
               }
            }
         }

         //Si no se cumple la expresión genera un error y evitara la inserción
         if (!preg_match("/^[a-zA-Z]{1,50}$/", $nombre)) {
            $this->mensajes[] = [
               "campo" => "nombre",
               "tipo" => "danger",
               "mensaje" => "Nombre no valido."
            ];
            $errores["nombre"] = "Error: No valido";
            $parametros = ["mensajes" => $this->mensajes];
         }

         if (!preg_match("/^\d{8}[a-zA-Z]{1}$/", $dni)) {
            $this->mensajes[] = [
               "campo" => "dni",
               "tipo" => "danger",
               "mensaje" => "Dni no valido."
            ];
            $errores["nombre"] = "Error: No valido";
            $parametros = ["mensajes" => $this->mensajes];
         }

         if (!preg_match("/^[6-7]{1}[0-9]{8}$/", $telefono) && !preg_match("/^[8-9]{1}[0-9]{8}$/", $telefono)) {
            $this->mensajes[] = [
               "campo" => "telefono",
               "tipo" => "danger",
               "mensaje" => "Solo fijos nacionales o moviles."
            ];
            $errores["nombre"] = "Error: No valido";
            $parametros = ["mensajes" => $this->mensajes];
         }

         if (!preg_match("/[a-zA-Z0-9_]{1,100}/", $direccion)) {
            $this->mensajes[] = [
               "campo" => "direccion",
               "tipo" => "danger",
               "mensaje" => "Nombre no valido."
            ];
            $errores["nombre"] = "Error: No valido";
            $parametros = ["mensajes" => $this->mensajes];
         }

         if (count($errores) > 0) {
            $this->view->show("editarUsuario", $parametros);
         }

         // Si no se han producido errores realizamos el registro del usuario
         if (count($errores) == 0) {

            $resultModelo = $this->modelo->actuser([
               'nombre' => $nombre,
               "apellido1" => $apellido1,
               'apellido2' => $apellido2,
               'dni' => $dni,
               'direccion' => $direccion,
               'telefono' => $telefono,
               'usuario' => $_GET['usuario'], //Parametro opcional depende del metodo que haya ejecutado este.
               'password' => $password,
               'imagen' => $imagen
            ]);

            if ($resultModelo["correcto"]) :
               $this->mensajes[] = [
                  "tipo" => "success",
                  "mensaje" => "El usuarios se registró correctamente!!"
               ];
            else :
               $this->mensajes[] = [
                  "tipo" => "danger",
                  "mensaje" => "El usuario no pudo registrarse!! :( <br />({$resultModelo["error"]})"
               ];
            endif;
         } else {
            $this->mensajes[] = [
               "tipo" => "danger",
               "mensaje" => "Datos de registro de usuario erróneos!! :("
            ];
         }

         //Llamamos de nuevo al metodo que lista todos los usuarios al administrador.
         $this->listaUsuarios();
      }
   }


   /**
    * Funcion para listar todos los usuarios al administrador.
    */
   public function listaUsuarios()
   {

      if (isset($_SESSION['rol_id'])) {
         // Almacenamos en el array 'parametros[]'los valores que vamos a mostrar en la vista
         $parametros = [
            "datos" => NULL,
            "mensajes" => []
         ];

         $resultModelo = $this->modelo->listado();

         if ($resultModelo["correcto"]) {
            $parametros["datos"] = $resultModelo["datos"];

            $this->mensajes[] = [
               "tipo" => "success",
               "mensaje" => "El listado se realizó correctamente"
            ];
         } else {

            $this->mensajes[] = [
               "tipo" => "danger",
               "mensaje" => "El listado no pudo realizarse correctamente!! :( <br/>({$resultModelo["error"]})"
            ];
         }

         $parametros["mensajes"] = $this->mensajes;
         $this->view->show("ListarUsuarios", $parametros);
      } else {
         $this->view->show("inicio");
      }
   }


   /**
    * Funcion para listar al administrador los usuarios no validados.
    */
   public function listaUsuariosNoValidados()
   {

      if (isset($_SESSION['rol_id'])) {

         $parametros = [
            "datos" => NULL,
            "mensajes" => []
         ];

         $resultModelo = $this->modelo->listadoNoValidados();

         if ($resultModelo["correcto"]) {
            $parametros["datos"] = $resultModelo["datos"];
            $this->mensajes[] = [
               "tipo" => "success",
               "mensaje" => "Operación realizada correctamente"
            ];
         } else {
            $this->mensajes[] = [
               "tipo" => "danger",
               "mensaje" => "El listado no pudo realizarse correctamente!! :( <br/>({$resultModelo["error"]})"
            ];
         }

         $parametros["mensajes"] = $this->mensajes;
         $this->view->show("ListarUsuariosNoValidados", $parametros);
      } else {
         $this->view->show("inicio");
      }
   }

   /**
    * Funcion para cerrar sesion y devolver al inicio
    */
   public function cerrarSesion()
   {
      session_destroy();
      $this->view->show("inicio");
   }

   /**
    * Funcion para completar perfil. Discrimina en funcion del rol.
    */
   public function EditarPerfil()
   {
       if ($_SESSION['rol_id']==0) {
          $this->view->show("completarPerfilAdmin");
       } else {
          $this->view->show("completarPerfil");
       } 

   }

   /**
    * Metodo que permite al administrador cambiar el id a un usuario no validado.
    */
    public function actualizaruser()
    {
       $datos = [];
       //id del usuario a cambiar
       $datos['id'] = $_GET['id'];
       //id a poner.
       $datos['rol_id'] = $_GET['rol_id'];
 
       //Llamamos en el modelo al metodo para cambiarle el tipo de id.
       $this->modelo->actualizaruser($datos);
       $this->listaUsuariosNoValidados();
    }
}
