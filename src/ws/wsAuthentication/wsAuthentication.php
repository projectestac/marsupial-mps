<?php

require_once '../../../config.php';
require_once $CFG->dirroot . '/ws/lib.php';

if (!isset($_GET['wsdl'])) {
    // Save log registering the call to the ws server.
    add_to_log(1, 1);
    if ($CFG->debugmode) {
        add_to_log(1, '1-1', serialize(file_get_contents('php://input')), true);
    }
}

function generate_wsdl(): void
{
    global $CFG;

    if (!is_file("{$CFG->dataroot}/1/WebServices/wsAuthentication/wsAuthentication.wsdl")) {
        if ($CFG->debugmode && !isset($_GET['wsdl'])) {
            add_to_log(1, '1-2', '', true);
        }

        $strwsdl = '<?xml version="1.0" encoding="UTF-8"?>
            <definitions xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" xmlns:tns="http://educacio.gencat.cat/proveedores/autenticacion/" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" xmlns="http://schemas.xmlsoap.org/wsdl/" targetNamespace="http://educacio.gencat.cat/proveedores/autenticacion/">
            <types>
            <xsd:schema targetNamespace="http://educacio.gencat.cat/proveedores/autenticacion/">
             <xsd:import namespace="http://schemas.xmlsoap.org/soap/encoding/" />
             <xsd:import namespace="http://schemas.xmlsoap.org/wsdl/" />
             <xsd:element name="WSEAuthenticateHeader" type="tns:WSEAuthenticateHeader" />
               <xsd:complexType name="WSEAuthenticateHeader">
               <xsd:sequence>
                 <xsd:element minOccurs="0" maxOccurs="1" name="User" type="xsd:string" />
                 <xsd:element minOccurs="0" maxOccurs="1" name="Password" type="xsd:string" />
               </xsd:sequence>
               <xsd:anyAttribute />
             </xsd:complexType>
             <xsd:complexType name="AutenticarUsuarioContenido">
              <xsd:all>
               <xsd:element name="Credencial" type="xsd:string" minOccurs="1" maxOccurs="1"/>
               <xsd:element name="ISBN" type="xsd:string" minOccurs="1" maxOccurs="1"/>
               <xsd:element name="IdUsuario" type="xsd:string" minOccurs="1" maxOccurs="1"/>
               <xsd:element name="NombreApe" type="xsd:string" minOccurs="0" maxOccurs="1"/>
               <xsd:element name="IdGrupo" type="xsd:string" minOccurs="0" maxOccurs="1"/>
               <xsd:element name="Rol" type="tns:TipoRol" default="ESTUDIANTE" minOccurs="0" maxOccurs="1"/><xsd:element name="IdCurso" type="xsd:string" minOccurs="1" maxOccurs="1"/>
               <xsd:element name="IdCentro" type="xsd:string" minOccurs="1" maxOccurs="1"/>
               <xsd:element name="URLResultado" type="xsd:string" minOccurs="0" maxOccurs="1"/>
               <xsd:element name="IdContenidoLMS" type="xsd:string" minOccurs="0" maxOccurs="1"/>
               <xsd:element name="IdUnidad" type="xsd:string" minOccurs="0" maxOccurs="1"/>
               <xsd:element name="IdActividad" type="xsd:string" minOccurs="0" maxOccurs="1"/>
              </xsd:all>
             </xsd:complexType>
             <xsd:simpleType name="TipoRol">
                 <xsd:restriction base="xsd:string">
                     <xsd:enumeration value="ESTUDIANTE"/>
                     <xsd:enumeration value="PROFESOR"/>
                 </xsd:restriction>
             </xsd:simpleType>
             <xsd:complexType name="Licencia">
              <xsd:all>
               <xsd:element name="Codigo" type="xsd:string" minOccurs="0" maxOccurs="1"/>
               <xsd:element name="Descripcion" type="xsd:string" minOccurs="0" maxOccurs="1"/>
               <xsd:element name="URL" type="xsd:string" minOccurs="0" maxOccurs="1"/>
              </xsd:all>
             </xsd:complexType>
             <xsd:complexType name="AutenticarUsuarioContenidoResponse">
              <xsd:all>
               <xsd:element name="AutenticarUsuarioContenidoResult" type="tns:Licencia"/>
              </xsd:all>
             </xsd:complexType>
            </xsd:schema>
            </types>
            <message name="AutenticarUsuarioContenidoRequest">
              <part name="AutenticarUsuarioContenido" type="tns:AutenticarUsuarioContenido" /></message>
            <message name="AutenticarUsuarioContenidoResponse">
              <part name="return" type="tns:AutenticarUsuarioContenidoResponse" /></message>
            <wsdl:message name="AutenticarUsuarioContenidoWSEAuthenticateHeader">
              <part name="WSEAuthenticateHeader" element="tns:WSEAuthenticateHeader" />
            </wsdl:message>
            <portType name="ws_authenticationPortType">
              <operation name="AutenticarUsuarioContenido">
                <documentation>Retorna una URL de acceso al libro digital a partir de una credencial válida para ese libro.&lt;br /&gt;&lt;br /&gt;
        Parámetros: &lt;br /&gt;&lt;br /&gt;
         - Credencial = Código de credencial del usuario para ese libro. &lt;br /&gt;
         - ISBN = Código ISBN del libro digital al que se solicita acceso. &lt;br /&gt;
         - IdUsuario = Identificador único del usuario dentro del EVA. Longitud máxima de 20 caracteres. &lt;br /&gt;
         - NombreApe = Nombre y apellidos del usuario. Longitud máxima de 50 caracteres. &lt;br /&gt;
         - IdGrupo = Identificador del grupo del EVA del colegio desde donde se está solicitando el contenido. Longitud máxima de 30 caracteres. &lt;br /&gt;
         - IdCurso = Identificador del curso del EVA del colegio desde donde se está solicitando el contenido. Longitud máxima de 30 caracteres. &lt;br /&gt;
         - IdCentro = Identificador único que describe al colegio dentro del EVA. Longitud máxima de 100 caracteres. &lt;br /&gt;
         - URLResultado = Url del servicio al que se retorna el seguimiento de las actividades del libro.&lt;br /&gt;
         - IdContenidoLMS = Identificador del contenido en el EVA.&lt;br /&gt;
         - IdUnidad = Identificador de la unidad, acceso directo a una página del libro digital (donde solo se cargará la unidad seleccionada).&lt;br /&gt;
         - IdActividad = Identificador de una actividad del repositorio de contenido de la editorial. Esta llamada sirve como un acceso directo a esa actividad.&lt;br /&gt;&lt;br /&gt;
        Retorna: &lt;br /&gt;&lt;br /&gt;
         * Código (string) / Descripción (string) / URL (string) &lt;br /&gt;
             - (1): Proceso correcto / URL del libro devuelta correctamente. &lt;br /&gt;
             - (0): Error inesperado / URL de excepciones. &lt;br /&gt;
             - (-1): Error al realizar la URL dinámica / URL de excepciones. &lt;br /&gt;
             - (-2): El código de credencial no es válido / URL de excepciones. &lt;br /&gt;
             - (-3): El ISBN del producto no es válido / URL de excepciones. &lt;br /&gt;
             - (-4): La credencial ha expirado / URL de excepciones. &lt;br /&gt;
             - (-5): El identificador de la unidad no es válido / URL de excepciones. &lt;br /&gt;
             - (-6): El identificador de la actividad no es válido / URL de excepciones. &lt;br /&gt;
             - (-7): Rol incorrecto. El valor del rol es incorrecto / URL de excepciones. &lt;br /&gt;
             - (-101): Autenticación incorrecto. El usuario que solicita acceso a este método del servicio Web no es correcto. &lt;br /&gt;
             - (-102): Autenticación incorrecto. El usuario que solicita acceso a este método del servicio Web no tiene permisos.&lt;br /&gt;&lt;br /&gt;</documentation>
                <input message="tns:AutenticarUsuarioContenidoRequest"/>
                <output message="tns:AutenticarUsuarioContenidoResponse"/>
              </operation>
            </portType>
            <binding name="ws_authenticationBinding" type="tns:ws_authenticationPortType">
              <soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>
              <operation name="AutenticarUsuarioContenido">
                <soap:operation soapAction="http://educacio.gencat.cat/proveedores/autenticacion/#AutenticarUsuarioContenido" style="rpc"/>
                <input>
                    <soap:body use="literal" namespace="http://educacio.gencat.cat/proveedores/autenticacion/"/>
                    <soap:header message="tns:AutenticarUsuarioContenidoWSEAuthenticateHeader" part="WSEAuthenticateHeader" use="literal" />
                </input>
                <output><soap:body use="literal" namespace="http://educacio.gencat.cat/proveedores/autenticacion/"/></output>
              </operation>
            </binding>
            <service name="ws_authentication">
              <port name="ws_authenticationPort" binding="tns:ws_authenticationBinding">
                <soap:address location="' . $CFG->wwwroot . '/ws/wsAuthentication/wsAuthentication.php"/>
              </port>
            </service>
            </definitions>';

        if (!is_dir("{$CFG->dataroot}/1") && !mkdir("{$CFG->dataroot}/1") && !is_dir("{$CFG->dataroot}/1")) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', "{$CFG->dataroot}/1"));
        }
        if (!is_dir("{$CFG->dataroot}/1/WebServices") && !mkdir("{$CFG->dataroot}/1/WebServices") && !is_dir("{$CFG->dataroot}/1/WebServices")) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', "{$CFG->dataroot}/1/WebServices"));
        }
        if (!is_dir("{$CFG->dataroot}/1/WebServices/wsAuthentication") && !mkdir("{$CFG->dataroot}/1/WebServices/wsAuthentication") && !is_dir("{$CFG->dataroot}/1/WebServices/wsAuthentication")) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', "{$CFG->dataroot}/1/WebServices/wsAuthentication"));
        }
        $f = fopen("{$CFG->dataroot}/1/WebServices/wsAuthentication/wsAuthentication.wsdl", "w");
        fwrite($f, $strwsdl);
        fclose($f);
    }
}

class AutenticarUsuarioContenidoResponse
{
    public $AutenticarUsuarioContenidoResult;
}

class Licencia
{
    public $Codigo;
    public $Descripcion;
    public $URL;
}

function AutenticarUsuarioContenido($usrcontent)
{
    global $CFG;

    add_to_log(1, 10, serialize($usrcontent));

    $result = new AutenticarUsuarioContenidoResponse();
    $result->AutenticarUsuarioContenidoResult = new Licencia();

    $rawPostData = file_get_contents('php://input');
    $auth = UserAuthentication($rawPostData);

    if ($auth->Codigo === '1') {
        if (get_record('books_credentials', 'isbn', $usrcontent->ISBN)) {
            if ($book_credential = get_record('books_credentials', 'isbn', $usrcontent->ISBN, 'credentials', $usrcontent->Credencial)) {
                $result->AutenticarUsuarioContenidoResult->Codigo = $book_credential->code;
                $result->AutenticarUsuarioContenidoResult->Descripcion = $book_credential->description;
                $result->AutenticarUsuarioContenidoResult->URL = $book_credential->url;

                if (isset($usrcontent->Rol)) {
                    $alloweb_values = ["ESTUDIANTE", "PROFESOR"];
                    if (!in_array($usrcontent->Rol, $alloweb_values)) {
                        $result->AutenticarUsuarioContenidoResult->Codigo = "-7";
                        $result->AutenticarUsuarioContenidoResult->Descripcion = "Rol incorrecte. El valor del rol &eacute;s incorrecte";
                        $result->AutenticarUsuarioContenidoResult->URL = "http://www.xtec.cat/error.html";
                        return $result;
                    }
                } else {
                    $usrcontent->Rol = "ESTUDIANTE";
                }

                if ($book_credential->success == 1) {
                    $path = $CFG->wwwroot . '/data/books/';
                    if ($usrcontent->IdUnidad == '' && $usrcontent->IdActividad == '') {
                        if (!$bookpath = get_record('books', 'isbn', $usrcontent->ISBN)) {
                            add_to_log(1, '1-200', serialize(array('ISBN' => $usrcontent->ISBN)), true);
                        } else {
                            if ($bookpath->format == 'scorm') {
                                if (!manifest_manipulation($bookpath->path)) {
                                    add_to_log(1, '1-201', serialize(array('ISBN' => $usrcontent->ISBN, 'path' => $bookpath->path)), true);
                                }
                            }
                            $result->AutenticarUsuarioContenidoResult->URL = $path . $bookpath->path;
                        }
                    } else {
                        if ($usrcontent->IdUnidad != '' && $usrcontent->IdActividad == '') {
                            if (!$bookpath = get_record('books', 'isbn', $usrcontent->ISBN)) {
                                add_to_log(1, '1-210', serialize(['ISBN' => $usrcontent->ISBN]), true);
                            } else {
                                if (!$unitpath = get_record('books_units', 'bookid', $bookpath->id, 'code', $usrcontent->IdUnidad)) {
                                    add_to_log(1, '1-211', serialize(['ISBN' => $usrcontent->ISBN, 'unitcode' => $usrcontent->IdUnidad]), true);
                                    $result->AutenticarUsuarioContenidoResult->Codigo = "-5";
                                    $result->AutenticarUsuarioContenidoResult->Descripcion = "L'identificador de la unitat no &eacute;s v&agrave;lid";
                                    $result->AutenticarUsuarioContenidoResult->URL = "http://www.xtec.cat/error.html";
                                    return $result;
                                } else {
                                    if ($bookpath->format == 'scorm') {
                                        if (!manifest_manipulation($unitpath->path)) {
                                            add_to_log(1, '1-212', serialize(['ISBN' => $usrcontent->ISBN, 'unitcode' => $usrcontent->IdUnidad, 'path' => $unitpath->path]), true);
                                        }
                                    }
                                    $result->AutenticarUsuarioContenidoResult->URL = $path . $unitpath->path;
                                }
                            }
                        } else {
                            if ($usrcontent->IdUnidad != '' && $usrcontent->IdActividad != '') {
                                if (!$bookpath = get_record('books', 'isbn', $usrcontent->ISBN)) {
                                    add_to_log(1, '1-220', serialize(['ISBN' => $usrcontent->ISBN]), true);
                                } else {
                                    if (!$unitpath = get_record('books_units', 'bookid', $bookpath->id, 'code', $usrcontent->IdUnidad)) {
                                        add_to_log(1, '1-221', serialize(['ISBN' => $usrcontent->ISBN, 'unitcode' => $usrcontent->IdUnidad]), true);
                                        $result->AutenticarUsuarioContenidoResult->Codigo = '-5';
                                        $result->AutenticarUsuarioContenidoResult->Descripcion = "L'identificador de la unitat no &eacute;s v&agrave;lid";
                                        $result->AutenticarUsuarioContenidoResult->URL = "http://www.xtec.cat/error.html";
                                        return $result;
                                    } else {
                                        if (!$activitypath = get_record('books_activities', 'bookid', $bookpath->id, 'unitid', $unitpath->id, 'code', $usrcontent->IdActividad)) {
                                            add_to_log(1, '1-222', serialize(['ISBN' => $usrcontent->ISBN, 'unitcode' => $usrcontent->IdUnidad, 'activitycode' => $usrcontent->IdActividad, 'path' => $activitypath->path]), true);
                                            $result->AutenticarUsuarioContenidoResult->Codigo = '-6';
                                            $result->AutenticarUsuarioContenidoResult->Descripcion = "L'identificador de la activitat no &eacute;s v&agrave;lid ";
                                            $result->AutenticarUsuarioContenidoResult->URL = "http://www.xtec.cat/error.html";
                                            return $result;
                                        } else {
                                            if ($bookpath->format == 'scorm') {
                                                if (!manifest_manipulation($activitypath->path)) {
                                                    add_to_log(1, '1-223', serialize(['ISBN' => $usrcontent->ISBN, 'unitcode' => $usrcontent->IdUnidad, 'path' => $activitypath->path]), true);
                                                }
                                            }
                                            // Set the absolute path to the manifest.
                                            $result->AutenticarUsuarioContenidoResult->URL = $path . $activitypath->path;
                                        }
                                    }
                                }
                            } else {
                                // If no path is found send the generic one.
                                add_to_log(1, '1-204', serialize(['ISBN' => $book_credential->ISBN]));
                                $result->AutenticarUsuarioContenidoResult->URL = $book_credential->url;
                            }
                        }
                    }

                    if ($result->AutenticarUsuarioContenidoResult->Codigo == 1) {
                        if (isset($bookpath->format) && $bookpath->format === 'webcontent' && !isset($_GET['wsdl'])) {
                            $session = new stdClass();
                            $session->token = str_replace('.', '', uniqid('', true));
                            $session->isbn = $usrcontent->ISBN;
                            $session->userid = $usrcontent->IdUsuario;
                            $session->nameape = $usrcontent->NombreApe;
                            $session->groupid = $usrcontent->IdGrupo;
                            $session->courseid = $usrcontent->IdCurso;
                            $session->centerid = $usrcontent->IdCentro;
                            $session->wsurltracking = $usrcontent->URLResultado;
                            $session->lmscontentid = $usrcontent->IdContenidoLMS;
                            $session->unitid = $usrcontent->IdUnidad;
                            $session->activityid = $usrcontent->IdActividad;
                            $session->addtime = time();
                            $session->expiretime = time() + 86400;
                            $session->urlcontent = $result->AutenticarUsuarioContenidoResult->URL . "?token={$session->token}";

                            $session = addslashes_object($session);
                            $result->AutenticarUsuarioContenidoResult->URL .= "?token={$session->token}";

                            insert_record('sessions', $session);
                        }
                    }
                }
            } else {
                $result->AutenticarUsuarioContenidoResult->Codigo = '-2';
                $result->AutenticarUsuarioContenidoResult->Descripcion = 'El codi de llicencia no és vàlid.';
                $result->AutenticarUsuarioContenidoResult->URL = 'http://www.xtec.cat/error.html';
            }
        } else {
            $result->AutenticarUsuarioContenidoResult->Codigo = '-3';
            $result->AutenticarUsuarioContenidoResult->Descripcion = 'El codi ISBN del producte no és vàlid.';
            $result->AutenticarUsuarioContenidoResult->URL = 'http://www.xtec.cat/error.html';
        }
    } else {
        $result->AutenticarUsuarioContenidoResult->Codigo = $auth->Codigo;
        $result->AutenticarUsuarioContenidoResult->Descripcion = $auth->Descripcion;
        $result->AutenticarUsuarioContenidoResult->URL = $auth->url ?? 'http://www.xtec.cat/error.html';
    }

    add_to_log(1, 20, serialize($result->AutenticarUsuarioContenidoResult));
    return $result;
}

function UserAuthentication($post_data): stdClass
{
    global $CFG;

    $retAut = new stdClass();
    $retAut->Codigo = '-101';
    $retAut->Descripcion = 'Usuari/contrasenya errònies';
    $retAut->url = 'http://www.xtec.cat/error.html';

    $post = rcommon_xml2array($post_data);

    if ($CFG->debugmode) {
        add_to_log(1, '1-11', serialize(rcommond_findarrayvalue($post, ['Envelope', 'Header', 'WSEAuthenticateHeader'])), true);
    }

    $keys = ["Envelope", "Header", "WSEAuthenticateHeader", "User", "value"];
    if (rcommond_findarrayvalue($post, $keys)) {
        $user_pub = rcommond_findarrayvalue($post, $keys);

        $keys = ["Envelope", "Header", "WSEAuthenticateHeader", "Password", "value"];
        $pwr_pub = rcommond_findarrayvalue($post, $keys);

        $data_to_log = new stdClass();
        $data_to_log->user = $user_pub;
        $data_to_log->pwd = $pwr_pub;
        add_to_log(1, 11, serialize($data_to_log));

        if ($creden_usr = get_record_sql("select * from {$CFG->prefix}lms_ws_credentials where username = '{$user_pub}' and password = '{$pwr_pub}'")) {
            $retAut->Codigo = $creden_usr->code;
            $retAut->Descripcion = $creden_usr->description;
        }
    }

    add_to_log(1, 21, serialize($retAut));

    return $retAut;
}

/*
 * Set all the href to absolute.
 *
 * @param string $path relative path to the manifest.xml
 * @return bool action finish ok or ko
 */
function manifest_manipulation($path): bool
{
    global $CFG;

    $dirpath = $CFG->dirroot . '/data/books/' . $path;
    $path = $CFG->wwwroot . '/data/books/' . $path;

    $topath = '';
    $separator = '/';
    $frompath = explode($separator, $path);
    for ($i = 0; $i < (count($frompath) - 1); $i++) {
        $topath .= $frompath[$i] . $separator;
    }

    if ($handle = fopen($dirpath, "r")) {

        $buffer = '';
        while (!feof($handle)) {
            $buffer .= fgets($handle, 4096);
        }

        $buffer = explode('href="', $buffer);
        $return = $buffer[0];
        for ($i = 1, $iMax = count($buffer); $i < $iMax; $i++) {
            // get relative url
            $stpos = strpos($buffer[$i], '"');
            $relurl = substr($buffer[$i], 0, $stpos);
            // transform relative url to absolute
            if (str_starts_with($relurl, 'http://')) {
                $relurl = explode($separator, $relurl);
                $relurl = $relurl[count($relurl) - 1];
            }
            $relurl = $topath . $relurl;
            // set actual row to return parameter
            $return .= 'href="' . $relurl . substr($buffer[$i], $stpos, strlen($buffer[$i]));
        }

        fclose($handle);

        $handle = fopen($dirpath, "w+");
        fwrite($handle, $return);
        fclose($handle);

        return true;
    }

    add_to_log(1, '1-202', serialize($path), true);
    add_to_log(1, '1-203', serialize($path), true);
    return false;
}

generate_wsdl();

// If the WSDL is requested directly by the browser, serve only the clean XML file and stop execution.
if (isset($_GET['wsdl'])) {
    header('Content-Type: application/xml; charset=utf-8');
    readfile("{$CFG->dataroot}/1/WebServices/wsAuthentication/wsAuthentication.wsdl");
    die;
}

// SOAP server implementation.
$server = new SoapServer("{$CFG->dataroot}/1/WebServices/wsAuthentication/wsAuthentication.wsdl", ['soap_version' => SOAP_1_1]);
$server->addFunction("AutenticarUsuarioContenido");
$server->handle();
die;
