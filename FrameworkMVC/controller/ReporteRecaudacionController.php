<?php

class ReporteRecaudacionController extends ControladorBase{

	public function __construct() {
		parent::__construct();
	}


	
	public function index(){
	
		session_start();
		$_id_usuarios= $_SESSION['id_usuarios'];
		//Creamos el objeto usuario
		$resultSet="";
		$registrosTotales = 0;
		$arraySel = "";
		$reporte_recaudacion = new ReporteRecaudacionModel(); 
		$usuarios = new UsuariosModel();
		
		$entidades = new EntidadesModel();
		$columnas_enc = "entidades.id_entidades,
  							entidades.nombre_entidades";
		$tablas_enc ="public.usuarios,
						  public.entidades";
		$where_enc ="entidades.id_entidades = usuarios.id_entidades AND usuarios.id_usuarios='$_id_usuarios'";
		$id_enc="entidades.nombre_entidades";
		$resultEnt=$entidades->getCondiciones($columnas_enc ,$tablas_enc ,$where_enc, $id_enc);
		
		if (isset(  $_SESSION['usuario_usuarios']) )
		{
			$permisos_rol = new PermisosRolesModel();
			$nombre_controladores = "ReporteRecaudacion";
			$id_rol= $_SESSION['id_rol'];
			$resultPer = $usuarios->getPermisosVer("   controladores.nombre_controladores = '$nombre_controladores' AND permisos_rol.id_rol = '$id_rol' " );
	
			if (!empty($resultPer))
			{
					
				if(isset($_POST["id_entidades"])){
	
					$id_entidades=$_POST['id_entidades'];
					$ruc_clientes=$_POST['ruc_clientes'];
					$razon_social_clientes=$_POST['razon_social_clientes'];
					$numero_credito_amortizacion_cabeza=$_POST['numero_credito_amortizacion_cabeza'];
					$numero_pagare_amortizacion_cabeza=$_POST['numero_pagare_amortizacion_cabeza'];
										
					$columnas = " recaudacion.id_recaudacion, 
								  fc_clientes.ruc_clientes, 
								  fc_clientes.razon_social_clientes, 
								  entidades.ruc_entidades, 
								  entidades.nombre_entidades, 
								  entidades.telefono_entidades, 
								  entidades.direccion_entidades, 
								  entidades.ciudad_entidades, 
								  entidades.logo_entidades, 
								  amortizacion_cabeza.numero_credito_amortizacion_cabeza, 
								  amortizacion_cabeza.numero_pagare_amortizacion_cabeza, 
								  recaudacion.capital_pagado_recaudacion, 
								  recaudacion.numero_cuota_recaudacion, 
								  recaudacion.fecha_pago_recaudacion, 
								  amortizacion_detalle.numero_cuota_amortizacion_detalle, 
								  amortizacion_detalle.saldo_inicial_amortizacion_detalle, 
								  amortizacion_detalle.interes_amortizacion_detalle, 
								  amortizacion_detalle.amortizacion_amortizacion_detalle, 
								  amortizacion_detalle.pagos_amortizacion_detalle, 
								  amortizacion_detalle.fecha_pagos_amortizacion_detalle, 
								  amortizacion_detalle.interes_dias_amortizacion_detalle, 
								  recaudacion.nombre_entidad_financiera_recaudacion, 
								  recaudacion.numero_papeleta_recaudacion, 
								  recaudacion.concepto_pago_amortizacion";
					
	
	
					$tablas=" 	 public.recaudacion, 
								  public.amortizacion_detalle, 
								  public.fc_clientes, 
								  public.entidades, 
								  public.amortizacion_cabeza";
									
					$where="    amortizacion_detalle.id_amortizacion_detalle = recaudacion.id_amortizacion_detalle AND
								  fc_clientes.id_clientes = recaudacion.id_clientes AND
								  entidades.id_entidades = recaudacion.id_entidades AND
								  amortizacion_cabeza.id_amortizacion_cabeza = recaudacion.id_amortizacion_cabeza";
	
					$id="fc_clientes.ruc_clientes";
	
	
					$where_0 = "";
					$where_1 = "";
					$where_2 = "";
					$where_3 = "";
					$where_4 = "";
	
					if($id_entidades!=0){$where_0=" AND entidades.id_entidades='$id_entidades'";}
					
					if($ruc_clientes!=""){$where_1=" AND fc_clientes.ruc_clientes = '$ruc_clientes'";}
	
					if($razon_social_clientes!=""){$where_2=" AND fc_clientes.razon_social_clientes = '$razon_social_clientes'";}
	
					if($numero_credito_amortizacion_cabeza!=""){$where_3=" AND amortizacion_cabeza.numero_credito_amortizacion_cabeza = '$numero_credito_amortizacion_cabeza'";}
					
					if($numero_pagare_amortizacion_cabeza!=""){$where_4=" AND amortizacion_cabeza.numero_pagare_amortizacion_cabeza = '$numero_pagare_amortizacion_cabeza'";}
					
					
					$where_to  = $where . $where_0 . $where_1 . $where_2 . $where_3;
	
	
					//$resultSet=$ccomprobantes->getCondiciones($columnas ,$tablas , $where_to, $id);
	
					
					//comienza paginacion
					
					
					$action = (isset($_REQUEST['action'])&& $_REQUEST['action'] !=NULL)?$_REQUEST['action']:'';
					
					if($action == 'ajax')
					{
						$html="";
						$resultSet=$reporte_recaudacion->getCantidad("*", $tablas, $where_to);
						$cantidadResult=(int)$resultSet[0]->total;
							
						$page = (isset($_REQUEST['page']) && !empty($_REQUEST['page']))?$_REQUEST['page']:1;
							
						$per_page = 50; //la cantidad de registros que desea mostrar
						$adjacents  = 9; //brecha entre páginas después de varios adyacentes
						$offset = ($page - 1) * $per_page;
							
						$limit = " LIMIT   '$per_page' OFFSET '$offset'";
							
							
						$resultSet=$reporte_recaudacion->getCondicionesPag($columnas, $tablas, $where_to, $id, $limit);
							
						$count_query   = $cantidadResult;
							
						$total_pages = ceil($cantidadResult/$per_page);
						
						if ($cantidadResult>0)
						{
								
							//<th style="color:#456789;font-size:80%;"></th>
						
							$html.='<div class="pull-left">';
							$html.='<span class="form-control"><strong>Registros: </strong>'.$cantidadResult.'</span>';
							$html.='<input type="hidden" value="'.$cantidadResult.'" id="total_query" name="total_query"/>' ;
							$html.='</div><br>';
							$html.='<section style="height:425px; overflow-y:scroll;">';
							$html.='<table class="table table-hover">';
							$html.='<thead>';
							$html.='<tr class="info">';
							$html.='<th>Id</th>';
							$html.='<th>Ruc</th>';
							$html.='<th>Razon Social</th>';
							$html.='<th>Nro. Crédito</th>';
							$html.='<th>Nro. Pagaré </th>';
							$html.='<th>Cap. Pagado</th>';
							$html.='<th>Nro. Cuota</th>';
							$html.='<th>Saldo Inicial</th>';
							$html.='<th>Interes</th>';
							$html.='<th>Amortización</th>';
							$html.='<th>Pagos</th>';
							$html.='<th>Fecha</th>';
							$html.='<th>Interes Dias</th>';
							$html.='<th>Entidad Recauda</th>';
							$html.='<th>Nro. Papeleta</th>';
							$html.='<th>Concepto</th>';
							$html.='<th>Imprimir</th>';							
							$html.='</tr>';
							$html.='</thead>';
							$html.='<tbody>';
								
							foreach ($resultSet as $res)
							{
								//<td style="color:#000000;font-size:80%;"> <?php echo ;</td>
						
									
								$html.='<tr>';
								$html.='<td style="color:#000000;font-size:80%;">'.$res->id_recaudacion.'</td>';
								$html.='<td style="color:#000000;font-size:80%;">'.$res->ruc_clientes.'</td>';
								$html.='<td style="color:#000000;font-size:80%;">'.$res->razon_social_clientes.'</td>';
								$html.='<td style="color:#000000;font-size:80%;">'.$res->numero_credito_amortizacion_cabeza.'</td>';
								$html.='<td style="color:#000000;font-size:80%;">'.$res->numero_pagare_amortizacion_cabeza.'</td>';
								$html.='<td style="color:#000000;font-size:80%;">'.$res->capital_pagado_recaudacion.'</td>';
								$html.='<td style="color:#000000;font-size:80%;">'.$res->numero_cuota_amortizacion_detalle.'</td>';
								$html.='<td style="color:#000000;font-size:80%;">'.$res->saldo_inicial_amortizacion_detalle.'</td>';
								$html.='<td style="color:#000000;font-size:80%;">'.$res->interes_amortizacion_detalle.'</td>';
								$html.='<td style="color:#000000;font-size:80%;">'.$res->amortizacion_amortizacion_detalle.'</td>';
								$html.='<td style="color:#000000;font-size:80%;">'.$res->pagos_amortizacion_detalle.'</td>';
								$html.='<td style="color:#000000;font-size:80%;">'.$res->fecha_pagos_amortizacion_detalle.'</td>';
								$html.='<td style="color:#000000;font-size:80%;">'.$res->interes_dias_amortizacion_detalle.'</td>';
								$html.='<td style="color:#000000;font-size:80%;">'.$res->nombre_entidad_financiera_recaudacion.'</td>';
								$html.='<td style="color:#000000;font-size:80%;">'.$res->numero_papeleta_recaudacion.'</td>';
								$html.='<td style="color:#000000;font-size:80%;">'.$res->concepto_pago_amortizacion.'</td>';
								$html.='<td style="color:#000000;font-size:80%;"><span class="pull-right"><a href="index.php?controller=ReporteRecaudacion&action=ReporteRecaudacionIndividual&id_recaudacion='. $res->id_recaudacion .'" target="_blank"><i class="glyphicon glyphicon-print"></i></a></span></td>';
								$html.='</tr>';
									
						
						
							}
								
							$html.='</tbody>';
							$html.='</table>';
							$html.='</section>';
							$html.='<div class="table-pagination pull-right">';
							$html.=''. $this->paginate("index.php", $page, $total_pages, $adjacents).'';
							$html.='</div>';
							$html.='</section>';
								
						
						}else{
						
							$html.='<div class="alert alert-warning alert-dismissable">';
							$html.='<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
							$html.='<h4>Aviso!!!</h4> No hay datos para mostrar';
							$html.='</div>';
						
						}
							
						echo $html;
						die();
						
					}
					
					if(isset($_POST['reporte']))
					{
						
						//parametros q van al servidor de reportes
						
						$parametros = array();
						$parametros['id_entidades']=isset($_POST['id_entidades'])?trim($_POST['id_entidades']):'';
						$parametros['ruc_clientes']=(isset($_POST['ruc_clientes']))?trim($_POST['ruc_clientes']):'';
						$parametros['razon_social_clientes']=(isset($_POST['razon_social_clientes']))?trim($_POST['razon_social_clientes']):'';
						$parametros['numero_credito_amortizacion_cabeza']=(isset($_POST['numero_credito_amortizacion_cabeza']))?trim($_POST['numero_credito_amortizacion_cabeza']):'';
						$parametros['numero_pagare_amortizacion_cabeza']=(isset($_POST['numero_pagare_amortizacion_cabeza']))?trim($_POST['numero_pagare_amortizacion_cabeza']):'';
					
						//para local 
						$pagina="conReporteRecaudacion.aspx";
												
						$conexion_rpt = array();
						$conexion_rpt['pagina']=$pagina;
						//$conexion_rpt['port']="59584";
						
						$this->view("ReporteRpt", array(
								"parametros"=>$parametros,"conexion_rpt"=>$conexion_rpt
						));
						
						die();
						
					}
					
	
		}
	
				
				$this->view("ReporteRecaudacion",array(
						"resultSet"=>$resultSet, 
						"resultEnt"=>$resultEnt 
						
							
							
				));
	
	
			}
			else
			{
				$this->view("Error",array(
						"resultado"=>"No tiene Permisos de Acceso a Reporte Recaudacion"
	
				));
	
				exit();
			}
	
		}
		else
		{
			$this->view("ErrorSesion",array(
					"resultSet"=>""
	
			));
	
		}
	
	}
	public function ReporteRecaudacionIndividual()
	{
	
		if(isset($_REQUEST['id_recaudacion']))
		{
	
	
	
			$parametros = array();
			$parametros['id_recaudacion']=isset($_GET['id_recaudacion'])?trim($_GET['id_recaudacion']):'';
	
	
			//aqui poner la pagina
	
			$pagina="conReporteRecaudacionIndividual.aspx";
	
			$conexion_rpt = array();
			$conexion_rpt['pagina']=$pagina;
			//$conexion_rpt['port']="59584";
	
			$this->view("ReporteRpt", array(
					"parametros"=>$parametros,"conexion_rpt"=>$conexion_rpt
			));
	
	
		}
	
	
	}
	
	public function paginate($reload, $page, $tpages, $adjacents) {
	
		$prevlabel = "&lsaquo; Prev";
		$nextlabel = "Next &rsaquo;";
		$out = '<ul class="pagination pagination-large">';
	
		// previous label
	
		if($page==1) {
			$out.= "<li class='disabled'><span><a>$prevlabel</a></span></li>";
		} else if($page==2) {
			$out.= "<li><span><a href='javascript:void(0);' onclick='load_usuarios(1)'>$prevlabel</a></span></li>";
		}else {
			$out.= "<li><span><a href='javascript:void(0);' onclick='load_usuarios(".($page-1).")'>$prevlabel</a></span></li>";
	
		}
	
		// first label
		if($page>($adjacents+1)) {
			$out.= "<li><a href='javascript:void(0);' onclick='load_usuarios(1)'>1</a></li>";
		}
		// interval
		if($page>($adjacents+2)) {
			$out.= "<li><a>...</a></li>";
		}
	
		// pages
	
		$pmin = ($page>$adjacents) ? ($page-$adjacents) : 1;
		$pmax = ($page<($tpages-$adjacents)) ? ($page+$adjacents) : $tpages;
		for($i=$pmin; $i<=$pmax; $i++) {
			if($i==$page) {
				$out.= "<li class='active'><a>$i</a></li>";
			}else if($i==1) {
				$out.= "<li><a href='javascript:void(0);' onclick='load_usuarios(1)'>$i</a></li>";
			}else {
				$out.= "<li><a href='javascript:void(0);' onclick='load_usuarios(".$i.")'>$i</a></li>";
			}
		}
	
		// interval
	
		if($page<($tpages-$adjacents-1)) {
			$out.= "<li><a>...</a></li>";
		}
	
		// last
	
		if($page<($tpages-$adjacents)) {
			$out.= "<li><a href='javascript:void(0);' onclick='load_usuarios($tpages)'>$tpages</a></li>";
		}
	
		// next
	
		if($page<$tpages) {
			$out.= "<li><span><a href='javascript:void(0);' onclick='load_usuarios(".($page+1).")'>$nextlabel</a></span></li>";
		}else {
			$out.= "<li class='disabled'><span><a>$nextlabel</a></span></li>";
		}
	
		$out.= "</ul>";
		return $out;
	}
	
			
}
?>