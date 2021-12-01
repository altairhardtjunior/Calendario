<?php
Class Usuario{
	private $pdo;
	public $msgErro = "";
//------------------------------------Funções Sistema--------------------------------------------------------------
	public function conectar(){
		global $pdo;
      $dbnome ="ipresbs";
      $host = "localhost";
      $usuario = "root";
      $senha = "";
		try {
			$pdo = new PDO("mysql:dbname=".$dbnome.";host=".$host,$usuario, $senha);
		} catch (PDOException $e) {
			$msgErro = $e->getMessage();

			
		}		

	}
	public function cadastrar($nome, $telefone, $email, $senha){
		global $pdo;
		//verificar se ja existe email cadastrado
		$sql = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = :e");
		$sql->bindValue(":e",$email);
		$sql->execute();
		if($sql->rowCount() > 0){
			return false;
		}else{
			//caso não cadastrado
			$sql = $pdo->prepare("INSERT INTO usuarios(nome, telefone, email, senha) VALUES(:n,:t,:e,:s)");
			$sql->bindValue(':n',$nome);
			$sql->bindValue(':t', $telefone);
			$sql->bindValue(':e', $email);
			$sql->bindValue(':s', md5($senha));
			$sql->execute();
			return true;
		}

	}
   public function cadastrar_ativo($matricula, $nome, $cpf, $nascimento, $nome_mae, $cargo, $data_ingresso, $orgao){
		global $pdo;
	
		
      //caso não cadastrado
      $sql = $pdo->prepare("INSERT INTO servidores(matricula, nome, cpf, nascimento, nome_mae, cargo, data_ingresso, orgao) VALUES(:m,:n,:c,:b,:a,:f,:d,:o)");
      $sql->bindValue(':m',$matricula);
      $sql->bindValue(':n',$nome);
      $sql->bindValue(':c',$cpf);
      $sql->bindValue(':b',$nascimento);
      $sql->bindValue(':a',$nome_mae);
      $sql->bindValue(':d',$data_ingresso);
      $sql->bindValue(':f',$cargo);
      $sql->bindValue(':o',$orgao);
      $sql->execute();
      return true;
   

	}
	public function logar($email, $senha){
		global $pdo;
		//*verifica se o email esta cadastrado*/
		$sql = $pdo->prepare("SELECT * FROM usuarios WHERE email = :e AND senha = :s");
		$sql->bindValue(":e", $email);
		$sql->bindValue(":s", md5($senha));
		$sql->execute();
		if($sql->rowCount() > 0){
			$dado = $sql->fetch();
			session_start();
			$_SESSION['id_usuario'] = $dado['id_usuario'];
			$_SESSION['nome'] = $dado['nome'];
            $_SESSION['nivel'] = $dado['nivel'];
			echo $dado;
			return true;
		}else{
			return false;
		}

///-------------------------------Funções para atendimentos Agendados-------------------------------
	}
	public function agendar_atendimento($nome, $cpf, $data, $hora, $motivo, $contato,$situacao,$responsavel){
		global $pdo;
		//verificar se ja existe email cadastrado
		$sql = $pdo->prepare("SELECT id_agendamento FROM agendar_atendimento WHERE nome = :n && data = :d ");
		$sql->bindValue(":n",$nome);
		$sql->bindValue(":d",$data);
		$sql->execute();
		if($sql->rowCount() > 0){
			return false;
		}else{
			
			$sql = $pdo->prepare("INSERT INTO agendar_atendimento(nome, cpf, data, hora,motivo,contato,situacao,responsavel) VALUES(:n,:f,:d,:h,:m,:c,:s,:r)");
			$sql->bindValue(':n',$nome);
            $sql->bindValue(':f',$cpf);
			$sql->bindValue(':d', $data);
			$sql->bindValue(':h', $hora);
			$sql->bindValue(':m', $motivo);
            $sql->bindValue(':c', $contato);
            $sql->bindValue(':s', $situacao);
			$sql->bindValue(':r', $responsavel);

			$sql->execute();
			return true;
		}

	}
	//função que busca e exibe os dados
    public function buscarDados(){
    	
    	global $pdo;
        $res = array();
        $cmd= $pdo->prepare("SELECT * FROM agendar_atendimento ORDER BY data DESC, hora ASC");
       $cmd->execute();
        $res = $cmd->fetchAll(PDO::FETCH_ASSOC);
       
        return $res;

    }
       //função excluir
     public function exclui_agendamento($id){
     	global $pdo;
        $cmd = $pdo->prepare("DELETE FROM agendar_atendimento WHERE id_agendamento = :id");
        $cmd->bindValue(":id", $id);
        $cmd->execute();
     }

     //função para buscar dados de uma pessoa
     public function buscarDadosUp($id){
     	global $pdo;
     	$res = array();
     	$cmd = $pdo->prepare("SELECT nome,cpf,data,hora,motivo,contato,situacao,responsavel FROM agendar_atendimento WHERE id_agendamento = :id");
     	$cmd->bindValue(":id",$id);
     	$cmd->execute();
     	$res = $cmd->fetch(PDO::FETCH_ASSOC);
     	return $res;

     }
     public function atualizaDados($id,$nome,$cpf,$data,$hora,$motivo,$contato,$situacao,$responsavel){
     	global $pdo;
     	$cmd = $pdo->prepare("UPDATE agendar_atendimento SET nome = :n, cpf= :f, data = :d, hora = :h, motivo = :m, contato = :c,situacao = :s, responsavel = :r WHERE id_agendamento = :id");
     	
     	$cmd->bindValue(":n",$nome);
        $cmd->bindValue(":f",$cpf);
     	$cmd->bindValue(":d",$data);
     	$cmd->bindValue(":h",$hora);
     	$cmd->bindValue(":m",$motivo);
        $cmd->bindValue(":c",$contato);
     	$cmd->bindValue(":r",$responsavel);
        $cmd->bindValue(":s",$situacao);
     	$cmd->bindValue(":id",$id);
     	$cmd->execute();  

 }

 //Agenda do dia
      public function atendimentoDia($data_busca){
     	global $pdo;
        $res = array();
        $cmd= $pdo->prepare("SELECT * FROM agendar_atendimento WHERE data = :d ORDER BY data ASC, hora ASC");
        $cmd->bindValue(":d",$data_busca);
        $cmd->execute();
        $res = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $res;

     }



///---------------------------Funções para registrar atendimento presencial-----------------------------------------
 	//----------Registrar atendimento-------------

    public function registrar_atendimento($nome, $cpf, $data, $hora, $motivo, $contato,$email,$origem,$responsavel){
        global $pdo;        
            
            $sql = $pdo->prepare("INSERT INTO registro_atendimento (nome, cpf, data, hora,motivo,contato,email,origem,responsavel) VALUES(:n,:f,:d,:h,:m,:c,:e,:s,:r)");
            $sql->bindValue(':n',$nome);
            $sql->bindValue(':f',$cpf);
            $sql->bindValue(':d', $data);
            $sql->bindValue(':h', $hora);
            $sql->bindValue(':m', $motivo);
            $sql->bindValue(':c', $contato);
            $sql->bindValue(':e', $email);
            $sql->bindValue(':s', $origem);
            $sql->bindValue(':r', $responsavel);

            $sql->execute();
            return true;
        

    }
    //função que busca e exibe os dados
    public function buscarDados_registro(){
        
        global $pdo;
        $res = array();
        $cmd= $pdo->prepare("SELECT * FROM registro_atendimento ORDER BY data DESC, hora ASC");
       $cmd->execute();
        $res = $cmd->fetchAll(PDO::FETCH_ASSOC);
       
        return $res;

    }
       //função excluir
     public function exclui_atendimento_registro($id){
        global $pdo;
        $cmd = $pdo->prepare("DELETE FROM registro_atendimento WHERE id = :id");
        $cmd->bindValue(":id", $id);
        $cmd->execute();
     }

     //função para buscar dados de uma pessoa
     public function buscarDadosUp_registro($id){
        global $pdo;
        $res = array();
        $cmd = $pdo->prepare("SELECT nome,cpf,data,hora,motivo,contato,email,origem,responsavel FROM registro_atendimento WHERE id = :id");
        $cmd->bindValue(":id",$id);
        $cmd->execute();
        $res = $cmd->fetch(PDO::FETCH_ASSOC);
        return $res;

     }
     public function atualizaDados_registro($id,$nome,$cpf,$data,$hora,$motivo,$contato,$email,$origem,$responsavel){
        global $pdo;
        $cmd = $pdo->prepare("UPDATE registro_atendimento SET nome = :n, cpf= :f, data = :d, hora = :h, motivo = :m, contato = :c,email= :e,origem = :s, responsavel = :r WHERE id = :id");
        
        $cmd->bindValue(":n",$nome);
        $cmd->bindValue(":f",$cpf);
        $cmd->bindValue(":d",$data);
        $cmd->bindValue(":h",$hora);
        $cmd->bindValue(":m",$motivo);
        $cmd->bindValue(":c",$contato);
        $cmd->bindValue(":e",$email);
        $cmd->bindValue(":r",$responsavel);
        $cmd->bindValue(":s",$origem);
        $cmd->bindValue(":id",$id);
        $cmd->execute();  

 }

 //Agenda do dia
      public function atendimentoDia_registro($data_busca){
        global $pdo;
        $res = array();
        $cmd= $pdo->prepare("SELECT * FROM registro_atendimento WHERE data = :d ORDER BY data ASC, hora ASC");
        $cmd->bindValue(":d",$data_busca);
        $cmd->execute();
        $res = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $res;

     }



     ///-----------------------Funções para agendar Pericias------------------------------------
    //----------Registrar pericia-------------

    public function agendar_pericia($nome, $cpf, $data, $hora, $hora_time, $contato, $contato_, $situacao,$responsavel){
        global $pdo;
        $sql = $pdo->prepare("INSERT INTO agenda_pericia(nome,cpf , data, hora, hora_time, contato, contato_, situacao, responsavel) VALUES(:n,:f,:d,:h,:j,:c,:t,:s,:r)");
        $sql->bindValue(':n',$nome);
        $sql->bindValue(':f',$cpf);
        $sql->bindValue(':d', $data);
        $sql->bindValue(':h', $hora);
        $sql->bindValue(':j', $hora_time);
        $sql->bindValue(':c', $contato);
        $sql->bindValue(':t', $contato_);
        $sql->bindValue(':s', $situacao);
        $sql->bindValue(':r', $responsavel);
        $sql->execute();
        return true;
    }
    //----buscar os dados atendimento presencial--------
    public function buscarDadosPericia(){
        
        global $pdo;
        $res = array();
        $cmd= $pdo->prepare("SELECT * FROM agenda_pericia where situacao = 'aguardando' ORDER BY data DESC, hora ASC");
       $cmd->execute();
        $res = $cmd->fetchAll(PDO::FETCH_ASSOC);
       
        return $res;

    }
    //------busca somente os dados do agendamento---------
    public function buscarDadosPericia_(){
        
      global $pdo;
      $res = array();
      $cmd= $pdo->prepare("SELECT * FROM agenda_pericia ORDER BY data DESC, hora ASC");
     $cmd->execute();
      $res = $cmd->fetchAll(PDO::FETCH_ASSOC);
     
      return $res;

  }
        //-------Excluir pericia----------------------
     public function exclui_pericia($id){
        global $pdo;
        $cmd = $pdo->prepare("DELETE FROM agenda_pericia WHERE id = :id and situacao != 'Realizada'");
        $cmd->bindValue(":id", $id);
        $cmd->execute();
     }
        //-----Atualizar agenda de Pericias------------------
     //--função para buscar dados de uma pessoa--
        public function buscarDadosUpPericia($id){
        global $pdo;
        $res = array();
        $cmd = $pdo->prepare("SELECT * FROM agenda_pericia WHERE id = :id");
        $cmd->bindValue(":id",$id);
        $cmd->execute();
        $res = $cmd->fetch(PDO::FETCH_ASSOC);
        return $res;

     }
     public function atualizaDadosPericia($id,$nome,$cpf,$data,$hora,$hora_time,$contato,$contato_,$responsavel){
        global $pdo;
        $cmd = $pdo->prepare("UPDATE agenda_pericia SET nome = :n, cpf= :f, data = :d, hora = :h, hora_time = :j, contato = :c, contato_ = :t, responsavel = :r WHERE id = :id");
        
        $cmd->bindValue(":n",$nome);
        $cmd->bindValue(":f",$cpf);
        $cmd->bindValue(":d",$data);
        $cmd->bindValue(":h",$hora);
        $cmd->bindValue(":j",$hora_time);
        $cmd->bindValue(":c",$contato);
        $cmd->bindValue(":t",$contato_);
        $cmd->bindValue(":r",$responsavel);
        $cmd->bindValue(":id",$id);
        $cmd->execute(); 
     }
     //Busca pericias por data
      public function periciaDia($data_busca){
        global $pdo;
        $res = array();
        $cmd= $pdo->prepare("SELECT * FROM agenda_pericia WHERE data = :d ORDER BY data ASC, hora ASC");
        $cmd->bindValue(":d",$data_busca);
        $cmd->execute();
        $res = $cmd->fetchAll(PDO::FETCH_ASSOC);

        return $res;

     }
   //################################################################################################
    //++++++++++++++++++++++++++++++++++busca de pericias e resultado de percia++++++++++++++++++++++
    #################################################################################################
     //Busca pericia por CPF
     public function buscar_pericia_cpf($cpf){
      global $pdo;
      $res = array();
      $cmd= $pdo->prepare("SELECT agenda_pericia.id, agenda_pericia.nome, agenda_pericia.data, agenda_pericia.hora, agenda_pericia.resultado, agenda_pericia.data_inicio, agenda_pericia.data_fim, calendario_pericia.tipo, calendario_pericia.perito, agenda_pericia.situacao from agenda_pericia JOIN calendario_pericia ON agenda_pericia.data = calendario_pericia.data WHERE agenda_pericia.cpf = :c AND agenda_pericia.hora_time BETWEEN calendario_pericia.inicio_time AND calendario_pericia.fim_time ORDER BY data DESC;");
     
      $cmd->bindValue(":c",$cpf);
      $cmd->execute();
      $res = $cmd->fetchAll(PDO::FETCH_ASSOC);
      return $res;
   }

   //Busca pericia por nome join calendario
   public function buscar_pericia_nome($nome){
      global $pdo;
      $res = array();
      $cmd= $pdo->prepare("SELECT agenda_pericia.id, agenda_pericia.nome, agenda_pericia.data, agenda_pericia.hora, agenda_pericia.resultado, agenda_pericia.data_inicio, agenda_pericia.data_fim, calendario_pericia.tipo, calendario_pericia.perito, agenda_pericia.situacao from agenda_pericia JOIN calendario_pericia ON agenda_pericia.data = calendario_pericia.data WHERE agenda_pericia.nome LIKE'%$nome%' AND agenda_pericia.hora_time BETWEEN calendario_pericia.inicio_time AND calendario_pericia.fim_time ORDER BY data DESC, hora ASC;");
      $cmd->bindValue(":n",$nome);
      $cmd->execute();
      $res = $cmd->fetchAll(PDO::FETCH_ASSOC);
      return $res;
   }
   //Busca pericia data join calendario
   public function buscar_pericia_data_($data_busca){
      global $pdo;
      $res = array();
      $cmd= $pdo->prepare("SELECT agenda_pericia.id, agenda_pericia.nome, agenda_pericia.data, agenda_pericia.hora, agenda_pericia.resultado, agenda_pericia.data_inicio, agenda_pericia.data_fim, calendario_pericia.tipo, calendario_pericia.perito, agenda_pericia.situacao from agenda_pericia JOIN calendario_pericia ON agenda_pericia.data = calendario_pericia.data WHERE agenda_pericia.data = :d AND agenda_pericia.hora_time BETWEEN calendario_pericia.inicio_time AND calendario_pericia.fim_time ORDER BY data DESC, hora ASC;");
      $cmd->bindValue(":d",$data_busca);
      $cmd->execute();
      $res = $cmd->fetchAll(PDO::FETCH_ASSOC);
      return $res;
   }

   ##############################
  //--------------------------reusultado de pericia----_------------------------ 
 
  //Buscar dados pericia para resultado
   public function buscar_dados_pericia_resultado($id_pericia){
      global $pdo;
      $cmd= $pdo->prepare("SELECT agenda_pericia.id, agenda_pericia.nome, agenda_pericia.data, agenda_pericia.hora, agenda_pericia.cpf, calendario_pericia.perito,  agenda_pericia.beneficio, agenda_pericia.data_inicio, agenda_pericia.data_fim, agenda_pericia.dias, agenda_pericia.portaria, agenda_pericia.resultado, agenda_pericia.oficio, agenda_pericia.obs, agenda_pericia.situacao from agenda_pericia JOIN calendario_pericia ON agenda_pericia.data = calendario_pericia.data WHERE agenda_pericia.id = :i AND agenda_pericia.hora_time BETWEEN calendario_pericia.inicio_time AND calendario_pericia.fim_time ORDER BY data DESC,hora ASC;");
      $cmd->bindValue(":i",$id_pericia);
      $cmd->execute();
      $res = $cmd->fetchAll(PDO::FETCH_ASSOC);
      return $res;
      
   }
   public function grava_resultado_pericia($id_pericia,$situacao,$beneficio,$data_inicio, $data_fim,$dias,$portaria,$resultado,$oficio,$obs){
		global $pdo;			
      $sql = $pdo->prepare("UPDATE agenda_pericia SET situacao= :s, beneficio = :b, data_inicio = :i , data_fim = :f, dias = :d ,portaria = :p, resultado = :r, oficio = :o, obs = :obs where id = $id_pericia");
      
      $sql->bindValue(':s', $situacao);
      $sql->bindValue(':b', $beneficio);
      $sql->bindValue(':i', $data_inicio);
      $sql->bindValue(':f', $data_fim);
      $sql->bindValue(':d', $dias);
      $sql->bindValue(':p', $portaria);
      $sql->bindValue(':r', $resultado);
      $sql->bindValue(':o', $oficio);
      $sql->bindValue(':obs', $obs);      
      $sql->execute();
      
		}
      public function pesquisa_resultado_exists($id_pericia){
         global $pdo;
         $cmd = $pdo->prepare("SELECT id_pericia FROM `resultado_pericia` WHERE id_pericia = :i");
         $cmd->bindValue(":i",$id_pericia);
         $cmd->execute();
         $busca = $cmd->fetchAll(PDO::FETCH_ASSOC);
         return $busca;

      }
   
   

    //------------Calendario de pericias--------------
     

     public function buscarCalendarioPericia(){        
        global $pdo;
        $res = array();
        $cmd= $pdo->prepare("SELECT id, data, tipo, perito, inicio, fim, intervalo, situacao FROM calendario_pericia ORDER BY data DESC ");
       $cmd->execute();
        $res = $cmd->fetchAll(PDO::FETCH_ASSOC);
       
        return $res;

    }
      public function buscarCalendarioPericia_(){        
        global $pdo;
        $teste = "em aberto";
        $res = array();
        $cmd= $pdo->prepare("SELECT id, data, tipo, inicio, fim, intervalo FROM calendario_pericia WHERE situacao = 'Agendando' ORDER BY data DESC ");
       $cmd->execute();
        $res = $cmd->fetchAll(PDO::FETCH_ASSOC);
       
        return $res;

    }

     
    public function exclui_dataPericia($id){
        global $pdo;
        $cmd = $pdo->prepare("DELETE FROM calendario_pericia WHERE id = :id");
        $cmd->bindValue(":id", $id);
        $cmd->execute();
     }

     public function buscarDataUpPericia($id){
        global $pdo;
        $res = array();
        $cmd = $pdo->prepare("SELECT * FROM calendario_pericia WHERE id = :id");
        $cmd->bindValue(":id",$id);
        $cmd->execute();
        $res = $cmd->fetch(PDO::FETCH_ASSOC);
        return $res;
    }
    public function atualizaDataPericia($id,$tipo,$perito,$data,$inicio,$fim,$inicio_time,$fim_time,$intervalo,$situacao){
        global $pdo;
        $cmd = $pdo->prepare("UPDATE calendario_pericia SET tipo = :t, perito= :p, data = :d, inicio = :i, fim = :f, inicio_time = :j, fim_time = :h, intervalo = :n, situacao = :s WHERE id = :id");
        
        $cmd->bindValue(":t",$tipo);
        $cmd->bindValue(":p",$perito);
        $cmd->bindValue(":d",$data);
        $cmd->bindValue(":i",$inicio);
        $cmd->bindValue(":f",$fim);
        $cmd->bindValue(':j',$inicio_time);
        $cmd->bindValue(':h',$fim_time);
        $cmd->bindValue(":n",$intervalo);
        $cmd->bindValue(":s",$situacao);
        $cmd->bindValue(":id",$id);
        $cmd->execute();
     }
     public function criaDataPericia($tipo,$perito,$data,$inicio,$fim,$inicio_time,$fim_time,$intervalo,$situacao){
        global $pdo;
        $cmd = $pdo->prepare("INSERT INTO calendario_pericia(tipo, perito, data, inicio, fim, inicio_time, fim_time, intervalo, situacao) VALUES(:t,:p,:d,:i,:f,:j,:h,:n,:s)");
        
        $cmd->bindValue(':t',$tipo);
        $cmd->bindValue(":p",$perito);
        $cmd->bindValue(':d',$data);
        $cmd->bindValue(':i',$inicio);
        $cmd->bindValue(':f',$fim);
        $cmd->bindValue(':j',$inicio_time);
        $cmd->bindValue(':h',$fim_time);
        $cmd->bindValue(':n',$intervalo);
        $cmd->bindValue(':s',$situacao);
        $cmd->execute();
        return true;
     }


     //_______________________________________________________________________________________
     //++++++++++++++++++++++FUNÇÕES DE IMPORTAÇÃO+++++++++++++++++++++++++
     public function importa_base($matricula, $nome, $cpf, $nascimento, $nome_mae,$cargo,$data_ingresso, $orgao){
        global $pdo;            
            $sql = $pdo->prepare("INSERT INTO servidores(matricula, nome,cpf,nascimento,nome_mae,cargo,data_ingresso,orgao) VALUES(:m,:n,:c,:d,:j,:e,:i,:o)");
            $sql->bindValue(':m',$matricula);
            $sql->bindValue(':n',$nome);
            $sql->bindValue(':c', $cpf);
            $sql->bindValue(':d', $nascimento);
            $sql->bindValue(':j', $nome_mae);
            $sql->bindValue(':e', $cargo);
            $sql->bindValue(':i', $data_ingresso);
            $sql->bindValue(':o', $orgao);

            $sql->execute();
            return true;
        }

    //_____________________________________________________________________________________________
        //++++++++++++++++++FUNÇOES SERVIDORES+++++++++++++++++++++++++++++
        public function buscarDadosSevidores(){
        
        global $pdo;
        $res = array();
        $cmd= $pdo->prepare("SELECT matricula,nome,cpf FROM servidores");
       $cmd->execute();
        $res = $cmd->fetchAll(PDO::FETCH_ASSOC);
       
        return $res;

    }
     
      public function buscar_servidor_nome($servidor){
        global $pdo;
        $res = array();
        $cmd= $pdo->prepare("SELECT matricula,nome,cpf FROM servidores WHERE nome :n");
        $cmd->bindValue(":n",$servidor);
        $cmd->execute();
        $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $result;

     }
     public function buscar_servidor_cpf($cpf){
        global $pdo;
        $res = array();
        $cmd= $pdo->prepare("SELECT matricula,nome,cargo,nascimento FROM servidores WHERE cpf = :c");
        $cmd->bindValue(":c",$cpf);
        $cmd->execute();
        $res = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $res;
     }
     public function buscar_servidor__nome($nome){
        global $pdo;
        $res = array();
        $cmd= $pdo->prepare("SELECT matricula,nome,cargo,cpf,nascimento FROM servidores WHERE nome LIKE '%$nome%'");
        $cmd->bindValue(":n",$nome);
        $cmd->execute();
        $res = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $res;
     }

     //____________________________________________________________________________
    ################################################################### 
    //++++++++++++++++++FUNÇOES dash_board+++++++++++++++++++++++++++++
    ####################################################################

        
     #-------------------Registro de atendimento----------------------------------
        public function buscar_origem($tipo){
        global $pdo;
        $agenda = $tipo;
        $res = array();
        $cmd= $pdo->prepare("SELECT count(*) FROM registro_atendimento WHERE origem = :a");
        $cmd->bindValue(":a",$agenda);
        $cmd->execute();
        $res = $cmd->fetch();
        return $res;
     }
      public function buscar_origem_periodo($tipo,$inicio,$fim){
        global $pdo;
        $agenda = $tipo;
        $inicio = $inicio;
        $fim = $fim;
        $res = array();
        $cmd= $pdo->prepare("SELECT count(*) FROM registro_atendimento WHERE origem = :a and data Between :i and :f");
        $cmd->bindValue(":a",$agenda);
        $cmd->bindValue(":i",$inicio);
        $cmd->bindValue(":f",$fim);
        $cmd->execute();
        $res = $cmd->fetch();
        return $res;
     }

      public function buscar_origem_total(){
        global $pdo;
        $res = array();
        $cmd= $pdo->prepare("SELECT count(*) FROM registro_atendimento ");
        $cmd->execute();
        $res = $cmd->fetch();
        return $res;
     }

      public function buscar_registro($tipo){
        global $pdo;
        $agenda = $tipo;
        $res = array();
        $cmd= $pdo->prepare("SELECT count(*) FROM registro_atendimento WHERE motivo = :a");
        $cmd->bindValue(":a",$agenda);
        $cmd->execute();
        $res = $cmd->fetch();
        return $res;
     }

     public function buscar_registro_periodo($tipo,$inicio,$fim){
        global $pdo;
        $agenda = $tipo;
        $inicio = $inicio;
        $fim = $fim;
        $res = array();
        $cmd= $pdo->prepare("SELECT count(*) FROM registro_atendimento WHERE motivo = :a and data Between :i and :f");
        $cmd->bindValue(":a",$agenda);
        $cmd->bindValue(":i",$inicio);
        $cmd->bindValue(":f",$fim);
        $cmd->execute();
        $res = $cmd->fetch();
        return $res;
     }

     public function buscar_Registro_total($inicio, $fim){
        global $pdo;
        $res = array();
        $cmd= $pdo->prepare("SELECT count(*) FROM registro_atendimento where data Between :i and :f");
        $cmd->bindValue(":i",$inicio);
        $cmd->bindValue(":f",$fim);
        $cmd->execute();
        $res = $cmd->fetch();
        return $res;
     }

     #-------------------Agenda----------------------------------
      public function buscar_agenda_total(){
        global $pdo;
        $res = array();
        $cmd= $pdo->prepare("SELECT count(*) FROM agendar_atendimento ");
        $cmd->execute();
        $res = $cmd->fetch();
        return $res;
     }
      public function buscar_agenda_situacao($tipo){
        global $pdo;
        $situacao = $tipo;
        $res = array();
        $cmd= $pdo->prepare("SELECT count(*) FROM agendar_atendimento WHERE situacao = :a");
        $cmd->bindValue(":a",$situacao);
        $cmd->execute();
        $res = $cmd->fetch();
        return $res;
     }

     public function buscar_agenda_situacao_periodo($tipo,$inicio,$fim){
        global $pdo;
        $situacao = $tipo;
        $inicio = $inicio;
        $fim = $fim;
        $res = array();
        $cmd= $pdo->prepare("SELECT count(*) FROM agendar_atendimento WHERE situacao = :a and data Between :i and :f");
        $cmd->bindValue(":a",$situacao);
        $cmd->bindValue(":i",$inicio);
        $cmd->bindValue(":f",$fim);
        $cmd->execute();
        $res = $cmd->fetch();
        return $res;
     }
     public function buscar_agenda_motivo($tipo){
        global $pdo;
        $situacao = $tipo;
        $res = array();
        $cmd= $pdo->prepare("SELECT count(*) FROM agendar_atendimento WHERE motivo = :a");
        $cmd->bindValue(":a",$situacao);
        $cmd->execute();
        $res = $cmd->fetch();
        return $res;
     }
     public function buscar_agenda_motivo_periodo($tipo,$inicio,$fim){
        global $pdo;
        $situacao = $tipo;
        $inicio = $inicio;
        $fim = $fim;
        $res = array();
        $cmd= $pdo->prepare("SELECT count(*) FROM agendar_atendimento WHERE motivo = :a and data Between :i and :f");
        $cmd->bindValue(":a",$situacao);
        $cmd->bindValue(":i",$inicio);
        $cmd->bindValue(":f",$fim);
        $cmd->execute();
        $res = $cmd->fetch();
        return $res;
     }

     public function buscar_agenda_periodo($inicio, $fim){
        global $pdo;
        $res = array();
        $cmd= $pdo->prepare("SELECT count(*) FROM agendar_atendimento where data Between :i and :f");
        $cmd->bindValue(":i",$inicio);
        $cmd->bindValue(":f",$fim);
        $cmd->execute();
        $res = $cmd->fetch();
        return $res;
     }
    #-------------------Pericia----------------------------------
      public function buscar_pericia_total(){
        global $pdo;
        $res = array();
        $cmd= $pdo->prepare("SELECT count(*) FROM agenda_pericia ");
        $cmd->execute();
        $res = $cmd->fetch();
        return $res;
     }

       public function buscar_pericia_situacao($tipo){
        global $pdo;
        $situacao = $tipo;
        $res = array();
        $cmd= $pdo->prepare("SELECT count(*) FROM agenda_pericia WHERE situacao = :a");
        $cmd->bindValue(":a",$situacao);
        $cmd->execute();
        $res = $cmd->fetch();
        return $res;
     }
     public function buscar_pericia_situacao_periodo($tipo,$inicio,$fim){
        global $pdo;
        $situacao = $tipo;
        $inicio = $inicio;
        $fim= $fim;
        $res = array();
        $cmd= $pdo->prepare("SELECT count(*) FROM agenda_pericia WHERE situacao = :a and data Between :i and :f");
        $cmd->bindValue(":a",$situacao);
        $cmd->bindValue(":i",$inicio);
        $cmd->bindValue(":f",$fim);
        $cmd->execute();
        $res = $cmd->fetch();
        return $res;
     }

     public function buscar_pericia_tipo($tipo){
        global $pdo;
        $pericia = $tipo;
        $res = array();
        $cmd= $pdo->prepare("SELECT count(*) FROM agenda_pericia WHERE situacao = :a");
        $cmd->bindValue(":a",$pericia);
        $cmd->execute();
        $res = $cmd->fetch();
        return $res;
     }
     public function buscar_pericia_periodo($inicio, $fim){
        global $pdo;
        $res = array();
        $cmd= $pdo->prepare("SELECT count(*) FROM agenda_pericia where data Between :i and :f");
        $cmd->bindValue(":i",$inicio);
        $cmd->bindValue(":f",$fim);
        $cmd->execute();
        $res = $cmd->fetch();
        return $res;
     }
     public function buscar_agendapericia_join_calendario(){
        global $pdo;
        $res = array();
        $cmd= $pdo->prepare("SELECT agenda_pericia.nome, agenda_pericia.data, calendario_pericia.tipo from agenda_pericia JOIN calendario_pericia on calendario_pericia.data = agenda_pericia.data");
        $cmd->execute();
        $res = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $res;
     }

     public function buscar_agendapericia_join_calendario_periodo($inicio,$fim){
        global $pdo;
        $inicio=$inicio;
        $fim=$fim;
        $res = array();
        $cmd= $pdo->prepare("SELECT agenda_pericia.nome, agenda_pericia.data, calendario_pericia.tipo from agenda_pericia JOIN calendario_pericia on calendario_pericia.data = agenda_pericia.data where calendario_pericia.data Between :i and :f");
        $cmd->bindValue(":i",$inicio);
        $cmd->bindValue(":f",$fim);
        $cmd->execute();
        $res = $cmd->fetchAll(PDO::FETCH_ASSOC);
        return $res;
     }

         

}
    

?>