<?php
  $host = "localhost";
  $user = "root";
  $pass = "";
  $db   = "testes";
  $connection = new MySQLi($host, $user, $pass, $db);
  if ($connection->connect_errno) {
    die("Erro na conexão: ".mysqli_connect_error());
  }
  mysqli_set_charset($connection, "utf8");
  $die = '';
  function getAddressData($connection, $code_contact, &$die) {
    $sql = "DELETE FROM enderecos WHERE logradouro IS NULL";
    if ($connection->query($sql) === TRUE) {
      $sql = "SELECT MAX(codigo) AS 'max_codigo' FROM enderecos";
      $result = mysqli_query($connection, $sql);
      if (!$result) {
        $die = "A consulta SQL falhou: ".mysqli_error($connection);
      } else {
        $rows = mysqli_fetch_array($result, MYSQLI_ASSOC);
        if (empty($rows['max_codigo'])) {
          $max_value = 1;   
        } else {
          $max_value = $rows['max_codigo'];
        }			  
        $sql = "ALTER TABLE enderecos AUTO_INCREMENT = " . $max_value;
        if ($connection->query($sql) === FALSE) {
          $die = "Algo saiu errado: ".mysqli_error($connection);   
        }
      }	
    } else {
      $die = "Algo saiu errado: ".mysqli_error($connection);
    }
    $sql = "SELECT * FROM enderecos WHERE codigo_contato = " . $code_contact;
    $result = mysqli_query($connection, $sql);
    if (!$result) {
      $die = "A consulta SQL falhou: ".mysqli_error($connection);
    } else {
      $rows = array();
      $i = 0;
      while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $rows[$i] = $row;
        $i++;
      }
    }
    return $rows;
  }
  function getPhoneData($connection, $code_contact, &$die) {
    $sql = "DELETE FROM telefones WHERE numero IS NULL";
    if ($connection->query($sql) === TRUE) {
      $sql = "SELECT MAX(codigo) AS 'max_codigo' FROM telefones";
      $result = mysqli_query($connection, $sql);
      if (!$result) {
        $die = "A consulta SQL falhou: ".mysqli_error($connection);
      } else {
        $rows = mysqli_fetch_array($result, MYSQLI_ASSOC);
        if (empty($rows['max_codigo'])) {
          $max_value = 1;   
        } else {
          $max_value = $rows['max_codigo'];
        }			  
        $sql = "ALTER TABLE telefones AUTO_INCREMENT = " . $max_value;
        if ($connection->query($sql) === FALSE) {
          $die = "Algo saiu errado: ".mysqli_error($connection);   
        }
      }	
    } else {
      $die = "Algo saiu errado: ".mysqli_error($connection);
    }
    $sql = "SELECT * FROM telefones WHERE codigo_contato = " . $code_contact;
    $result = mysqli_query($connection, $sql);
    if (!$result) {
      $die = "A consulta SQL falhou: ".mysqli_error($connection);
    } else {
      $rows = array();
      $i = 0;
      while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $rows[$i] = $row;
        $i++;
      }
    }
    return $rows;
  }
  if (isset($_GET['code'])) { 	 
    if (isset($_GET['delete'])) {
      // operação de exclusão
      if ($_GET['delete'] == "contact") {
        // em contatos
        $sql = "DELETE FROM contatos WHERE codigo = ".$_GET['code'];
        if ($connection->query($sql) === TRUE) {
          $sql = "ALTER TABLE contatos AUTO_INCREMENT = " . (intval($_GET['code']) -1);
          if ($connection->query($sql) === TRUE) {
            $code_contact = $_GET['code'];
          } else {		   
            $die = "Algo saiu errado: ".mysqli_error($connection);   
          }
        } else {
          $die = "Não foi possível remover: ".mysqli_error($connection);
        }
      } elseif ($_GET['delete'] == "address") {
        // em endereços
        $sql = "SELECT codigo_contato FROM enderecos WHERE codigo = ".$_GET['code'];
        $result = mysqli_query($connection, $sql);
        if (!$result) {
           $die = "A consulta SQL falhou: ".mysqli_error($connection);
        } else {
          $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
          if (!empty($row)) {
            $code_contact = $row['codigo_contato'];
            $sql = "DELETE FROM enderecos WHERE codigo = ".$_GET['code'];
            if ($connection->query($sql) === TRUE) {
              $sql = "ALTER TABLE enderecos AUTO_INCREMENT = " . (intval($_GET['code']) -1);
              if ($connection->query($sql) === TRUE) {
                $code_address = $_GET['code'];
              } else {		   
                $die = "Algo saiu errado: ".mysqli_error($connection);   
              }
            } else {
              $die = "Não foi possível remover: ".mysqli_error($connection);
            }
          } else {
            $code_address = $_GET['code'];    
            $code_contact = $_GET['code_contact'];
          }
        }
      } elseif ($_GET['delete'] == "phone") {
        // em telefones
        $sql = "SELECT codigo_contato FROM telefones WHERE codigo = ".$_GET['code'];
        $result = mysqli_query($connection, $sql);
        if (!$result) {
          $die = "A consulta SQL falhou: ".mysqli_error($connection);
        } else {
          $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
          if (!empty($row)) {
            $code_contact = $row['codigo_contato'];
            $sql = "DELETE FROM telefones WHERE codigo = ".$_GET['code'];
            if ($connection->query($sql) === TRUE) {
              $sql = "ALTER TABLE telefones AUTO_INCREMENT = " . (intval($_GET['code']) -1);
              if ($connection->query($sql) === TRUE) {
                $code_phone = $_GET['code'];
              } else {		   
                $die = "Algo saiu errado: ".mysqli_error($connection);   
              }
            } else {
              $die = "Não foi possível remover: ".mysqli_error($connection);
            }
          } else {
            $code_phone   = $_GET['code'];
            $code_contact = $_GET['code_contact'];
          }
        }
      } else {
        header('HTTP/1.0 400 Bad Request');
        require("./errors/400.html");
        exit; 
      } 	  
    } elseif (isset($_GET['update'])) {
      // operação de alteração
      if ($_GET['update'] == "contact") {
        // em contatos
        $code_contact = $_GET['code'];
        $sql = "SELECT codigo FROM contatos WHERE codigo = ".$_GET['code'];
        $result = mysqli_query($connection, $sql);
        if (!$result) {
          $die = "A consulta SQL falhou: ".mysqli_error($connection);
        } else {
          $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
          if (empty($row)) {
            $name = "'" . $_POST['name'] . "'";
            $surname = "'" . $_POST['surname'] . "'";
            $email = "'" . $_POST['email'] . "'";
            $sql  = "INSERT INTO contatos (codigo, nome, sobrenome, email) ";
            $sql .= "VALUES ($code_contact, $name, $surname, $email)";
            if ($connection->query($sql) === FALSE) {
              $die = "Não foi possível incluir: ".mysqli_error($connection);
            }
          } else {
            $code_contact = $row['codigo'];
            $sql  = "UPDATE contatos SET nome = '".$_POST['name']."', sobrenome = '".$_POST['surname']."', email = '".$_POST['email']."'";
            $sql .= " WHERE codigo = ".$code_contact;
            if ($connection->query($sql) === FALSE) {
              $die = "Não foi possível alterar: ".mysqli_error($connection);
            }
          }
        }
      } elseif ($_GET['update'] == "address") {
        // em endereços
        $code_address = $_GET['code'];
        $code_contact = $_GET['code_contact'];
        $sql = "SELECT codigo, codigo_contato FROM enderecos WHERE codigo = ".$_GET['code'];
        $result = mysqli_query($connection, $sql);
        if (!$result) {
          $die = "A consulta SQL falhou: ".mysqli_error($connection);
        } else {
          $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
          if (empty($row)) {
            $cep = "'" . $_POST['cep'] . "'";
            $street = "'" . $_POST['street'] . "'";
            $number = "'" . $_POST['number'] . "'";
            $complement = "'" . $_POST['complement'] . "'";
            $district = "'" . $_POST['district'] . "'";
            $city = "'" . $_POST['city'] . "'";
            $uf = "'" . $_POST['uf'] . "'";
            $sql  = "INSERT INTO enderecos (codigo, codigo_contato, cep, logradouro, numero, complemento, bairro, cidade, uf) ";
            $sql .= "VALUES ($code_address, $code_contact, $cep, $street, $number, $complement, $district, $city, $uf)";
            if ($connection->query($sql) === FALSE) {
              $die = "Não foi possível incluir: ".mysqli_error($connection);
            }
          } else {
            $sql  = "UPDATE enderecos SET codigo_contato = " . $code_contact . ", cep = '" .$_POST['cep']. "', logradouro = '".$_POST['street']."', numero = '".$_POST['number']."', complemento = '".$_POST['complement']."', bairro = '" . $_POST['district'] ."', cidade = '" . $_POST['city'] . "', uf = '" . $_POST['uf'] . "'";
            $sql .= " WHERE codigo = ".$_GET['code'];
            if ($connection->query($sql) === FALSE) {
              $die = "Não foi possível alterar: ".mysqli_error($connection);
            }            
          }
        }
      } elseif ($_GET['update'] == "phone") {
        // em telefones
        $code_phone = $_GET['code'];
        $code_contact = $_GET['code_contact'];
        $sql = "SELECT codigo, codigo_contato FROM telefones WHERE codigo = ".$_GET['code'];
        $result = mysqli_query($connection, $sql);
        if (!$result) {
          $die = "A consulta SQL falhou: ".mysqli_error($connection);
        } else {
          $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
          if (empty($row)) {
            $ddd = "'" . $_POST['ddd'] . "'";
            $number = "'" . $_POST['number'] . "'";
            $type = "'" . $_POST['type'] . "'";
            $sql  = "INSERT INTO telefones (codigo, codigo_contato, ddd, numero, tipo) ";
            $sql .= "VALUES ($code_phone, $code_contact, $ddd, $number, $type)";
            if ($connection->query($sql) === FALSE) {
              $die = "Não foi possível incluir: ".mysqli_error($connection);
            }
          } else {
            $sql  = "UPDATE telefones SET codigo_contato = " . $code_contact . ", ddd = '".$_POST['ddd']."', numero = '".$_POST['number']."', tipo = '".$_POST['type']."'";
            $sql .= " WHERE codigo = ".$_GET['code'];
            if ($connection->query($sql) === FALSE) {
              $die = "Não foi possível alterar: ".mysqli_error($connection);
            }
          }
        }
      } else {
        header('HTTP/1.0 400 Bad Request');
        require("./errors/400.html");
        exit;
      }
    } elseif (isset($_GET['insert'])) {
      // operação de inclusão com alteração
      if ($_GET['insert'] == "contact") {
        // em contatos  
        $sql = "SELECT * FROM contatos WHERE codigo = ".$_GET['code'] ;
        $result = mysqli_query($connection, $sql);
        if (!$result) {
          $die = "A consulta SQL falhou: ".mysqli_error($connection);
        } else {
          $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
          if (!empty($row)) {
            $code_contact = $row['codigo'];
            $name = $row['nome'];
            $surname = $row['sobrenome'];
            $email = $row['email'];
            $rows_phone = getPhoneData($connection, $code_contact, $die);
            $rows_address = getAddressData($connection, $code_contact, $die);
          } else {
            $code_contact = $_GET['code'];
            $name = $surname = $email = '';
            $rows_phone = $rows_address = array();
          }  
        }
      } elseif ($_GET['insert'] == "address") {
        // em endereços
        $sql = "SELECT * FROM enderecos WHERE codigo = ".$_GET['code'] ;
        $result = mysqli_query($connection, $sql);
        if (!$result) {
          $die = "A consulta SQL falhou: ".mysqli_error($connection);
        } else {
          $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
          if (!empty($row)) {
            $code_address = $row['codigo'];
            $code_contact = $row['codigo_contato'];
            $cep = $row['cep'];
            $street = $row['logradouro'];
            $number = $row['numero'];
            $complement = $row['complemento'];
            $district = $row['bairro'];
            $city = $row['cidade'];
            $uf = $row['uf'];
          } else {
            $code_address = $_GET['code'];
            $code_contact = $_GET['code_contact'];
            $cep = $street = $number = $complement = $district = $city = $uf = '';
          }  
        }
      } elseif ($_GET['insert'] == "phone") {
        // em telefones  
        $sql = "SELECT * FROM telefones WHERE codigo = ".$_GET['code'] ;
        $result = mysqli_query($connection, $sql);
        if (!$result) {
          $die = "A consulta SQL falhou: ".mysqli_error($connection);
        } else {
          $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
          if (!empty($row)) {
            $code_phone   = $row['codigo'];
            $code_contact = $row['codigo_contato'];
            $ddd = $row['ddd'];
            $number = $row['numero'];
            $type = $row['tipo'];
          } else {
            $code_phone   = $_GET['code'];
            $code_contact = $_GET['code_contact'];
            $ddd = $number = $type = '';
          }  
        }
      } else{
        header('HTTP/1.0 400 Bad Request');
        require("./errors/400.html");
        exit;
      }		  
    } else {
      header('HTTP/1.0 400 Bad Request');
      require("./errors/400.html");
      exit;
    }
  } elseif (isset($_GET['insert'])) {
    // operação de inclusão
    if ($_GET['insert'] == "contact") {
      // em contatos
      $sql = "INSERT INTO contatos (nome, sobrenome, email) ";
      $sql .= "VALUES (NULL, NULL, NULL)";
      if ($connection->query($sql) === TRUE) {
        $code_contact = $connection->insert_id;
        $name = $surname = $email = '';
      } else {
        $die = "Não foi possível incluir: ".mysqli_error($connection);
      }
      $rows_phone = getPhoneData($connection, $code_contact, $die);
      $rows_address = getAddressData($connection, $code_contact, $die); 
    } else {
      if (isset($_GET['code_contact'])) { 		
        $code_contact = $_GET['code_contact'];
        if ($_GET['insert'] == "address") {
          // em endereços
          $sql = "INSERT INTO enderecos (codigo_contato, cep, logradouro, numero, complemento, bairro, cidade, uf) ";
          $sql .= "VALUES (" . $_GET['code_contact'] . ", NULL, NULL, NULL, NULL, NULL, NULL, NULL)";
          if ($connection->query($sql) === TRUE) {
            $code_address = $connection->insert_id;
            $cep = $street = $number = $complement = $district = $city = $uf  = '';
          } else {
            $die = "Não foi possível incluir: ".mysqli_error($connection);
          }
        } elseif ($_GET['insert'] == "phone") {
          // em telefones
          $sql = "INSERT INTO telefones (codigo_contato, ddd, numero, tipo) ";
          $sql .= "VALUES (" . $_GET['code_contact'] . ", NULL, NULL, NULL)";
          if ($connection->query($sql) === TRUE) {
            $code_phone = $connection->insert_id;
            $ddd = $number = $type = '';
          } else {
            $die = "Não foi possível incluir: ".mysqli_error($connection);
          }
        } else {
          header('HTTP/1.0 400 Bad Request');
          require("./errors/400.html");
          exit;
        }
      } else {
        header('HTTP/1.0 400 Bad Request');
        require("./errors/400.html");
        exit;
      }
    }
  } elseif (empty($_GET)) {
    // Listagem de contatos
    $sql = "DELETE FROM contatos WHERE email IS NULL";
    if ($connection->query($sql) === TRUE) {
      $sql = "SELECT MAX(codigo) AS 'max_codigo' FROM contatos";
      $result = mysqli_query($connection, $sql);
      if (!$result) {
        $die = "A consulta SQL falhou: ".mysqli_error($connection);
      } else {
        $rows = mysqli_fetch_array($result, MYSQLI_ASSOC);
        if (empty($rows['max_codigo'])) {
          $max_value = 1;   
        } else {
          $max_value = $rows['max_codigo'];
        }			  
        $sql = "ALTER TABLE contatos AUTO_INCREMENT = " . $max_value;
        if ($connection->query($sql) === FALSE) {
          $die = "Algo saiu errado: ".mysqli_error($connection);   
        }
      }	
    } else {
      $die = "Algo saiu errado: ".mysqli_error($connection);
    }
    $sql = "SELECT * FROM contatos";
    $result = mysqli_query($connection, $sql);
    if (!$result) {
      $die = "A consulta SQL falhou: ".mysqli_error($connection);
    } else {
      $rows = array();
      $i = 0;
      while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $rows[$i] = $row;
        $i++;
      }
    }
  } else {
    header('HTTP/1.0 400 Bad Request');
    require("./errors/400.html");
    exit;
  }
?>
<html lang="pt">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Alexandre A. Barbosa, alexandre.araujo.barboza@gmail.com">
    <link href="./css/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="./font/awesome/css/all.css" rel="stylesheet">
    <script src="./js/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="./font/awesome/js/all.js"></script>
    <style>
      html, body {
        height: 100%;
      }
      .container {
        margin: 10px auto;
        height: 100%;
        display: flex;
        justify-content: center;
      }
      .row {
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
      }
      .board {
        margin: 10px;
        height: 125px;
        width: 400px;
        background-color: darkGreen;
        border: 1px solid black;
        font-size: 1.5em;
        font-weight: bold;
        color: white;
        text-align: center;
        line-height: 50px;
      }
      fieldset {
        background-color: #eeeeee;
      }
      legend {
        background-color: gray;
        color: white;
        padding: 5px 10px;
      }
    </style>
    <script type="text/javascript">  
      if (performance.navigation.type == 2){
        location.reload(true);
      }
    </script>
    <title>Contatos</title>
  </head>
  <body style="margin: 0; border:0; top:0; position: relative; padding: 0;">
  <?php if (!empty($die)) : ?>
    <!-- Ocorreu um erro -->
    <div class="container">
      <div class="row">
        <div class="col-xs-12 board" style="background: darkRed !important; height:auto !important; font-size: 1.2em !important; line-height: 35px !important; word-wrap: break-word;" ><?= $die ?></div>   
        <div class="col-xs-12 text-center">
          <a href="<?= $_SERVER['PHP_SELF'] ?>">Continuar</a>
        </div>
      </div>
    </div>
    <script>
      setTimeout(() => location.href="<?= $_SERVER['PHP_SELF'] ?>", 10000);
    </script>
  <?php elseif (isset($_GET['insert']) && $_GET['insert'] == "contact") : ?>
    <!-- Incluir Contato -->
    <script>
      function setCookieName() {
        let value = document.getElementById("name").value;
        document.cookie = "name=" + value;
      }
      function setCookieSurname() {
        let value = document.getElementById("surname").value;
        document.cookie = "surname=" + value;
      }
      function setCookieEmail() {
        let value = document.getElementById("email").value;
        document.cookie = "email=" + value;
      }
      <?php
        if (!empty($_COOKIE['name'])) 
          $name = $_COOKIE['name'];
        if (!empty($_COOKIE['surname'])) 
          $surname = $_COOKIE['surname'];
        if (!empty($_COOKIE['email'])) 
          $email = $_COOKIE['email'];
      ?>
    </script>
    <div class="d-flex justify-content-center">
      <form style= "width:400px;" action="?update=contact&code=<?=$code_contact?>" method="post" enctype="multipart/form-data">
       <h2 class="mb-3 mt-3"><pre><?= isset($_GET['code']) ? 'Alterar' : 'Incluir' ?> Contato:<span style="color:gray;">&nbsp;#<?=$code_contact?></span></pre></h2>
       <div class="mb-3">
          <label for="name" class="form-label">Nome</label>
          <input type="text" class="form-control" name="name" id="name" required maxlength="50" value="<?= $name ?>" onchange=setCookieName();>
        </div>
        <div class="mb-3">
          <label for="surname" class="form-label">Sobrenome</label>
          <input type="text" class="form-control" name="surname" id="surname" required maxlength="50" value="<?= $surname ?>" onchange=setCookieSurname();>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Endereço de e-mail</label>
          <input type="email" class="form-control" name="email" id="email" required maxlength="80" value="<?= $email ?>" onchange=setCookieEmail();>
        </div>
        <fieldset class="mb-3">
          <legend>
            Endereços
          </legend>
          <div class="table-responsive-md">
            <table class="table">
              <tr>
                <th>Logradouro</th>
                <th>Número</th>
                <th>UF</th>
                <th></th>
              </tr>
              <?php foreach ($rows_address as &$valor) : ?> 
                <tr>
                  <td><?= $valor['logradouro'] ?></td>
                  <td><?= $valor['numero'] ?></td>
                  <td><?= $valor['uf'] ?></td>
                  <td nowrap>
                    <a href="?insert=address&code=<?= $valor['codigo']?>&code_contact=<?= $code_contact ?>" title="atualizar"><i style="color:green;" class="fa-solid fa-pen-to-square"></i></a>
                    &nbsp;
                    <a href="?delete=address&code=<?= $valor['codigo'] ?>&code_contact=<?= $code_contact ?>"" title="remover"><i style="color:red;" class="fa-solid fa-trash"></i></a>
                  </td>
                </tr>
              <?php endforeach ; ?>
              <tr>
                <td style="text-align:center;" colspan="4">
                  <button type="button" class="btn btn-primary btn-sm" onclick="window.location.href='?insert=address&code_contact=<?= $code_contact ?>';">Incluir</button>
                </td>
              </tr>
            </table>
          </div>  
        </fieldset>
        <fieldset class="mb-3">
          <legend>
            Telefones
          </legend>
          <div class="table-responsive-md">
            <table class="table">
              <tr>
                <th>DDD</th>
                <th>Número</th>
                <th>Tipo</th>
                <th></th>
              </tr>
              <?php foreach ($rows_phone as &$valor) : ?> 
                <tr>
                  <td><?= $valor['ddd'] ?></td>
                  <td><?= $valor['numero'] ?></td>
                  <td><?= $valor['tipo'] ?></td>
                  <td nowrap>
                    <a href="?insert=phone&code=<?= $valor['codigo'] ?>&code_contact=<?= $code_contact ?>" title="atualizar"><i style="color:green;" class="fa-solid fa-pen-to-square"></i></a>
                    &nbsp;
                    <a href="?delete=phone&code=<?= $valor['codigo'] ?>&code_contact=<?= $code_contact ?>" title="remover"><i style="color:red;" class="fa-solid fa-trash"></i></a>
                  </td>
                </tr>
              <?php endforeach ; ?>
              <tr>
                <td style="text-align:center;" colspan="4">
                  <button type="button" class="btn btn-primary btn-sm" onclick="window.location.href='?insert=phone&code_contact=<?= $code_contact ?>';">Incluir</button>
                </td>
              </tr>
            </table>
          </div>  
        </fieldset>
        <div class="d-flex justify-content-center" style="width:400px;">
          <button type="button" class="col-md-5 btn btn-secondary" onclick=<?= isset($_GET['code']) ? "window.location.href='" .$_SERVER['PHP_SELF'] . "'" : "window.location.href='?delete=contact&code=" . $code_contact . "'" ?>>Cancelar</button>
          <div class="col-md-2">&nbsp;&nbsp;</div>
          <button type="submit" class="col-md-5 btn btn-primary">Gravar</button>
        </div>  
      </form>
    </div>
  <?php elseif (isset($_GET['insert']) && $_GET['insert'] == "address") : ?>
    <!-- Incluir Endereço -->
    <script>
      function clearCEP() {
        document.getElementById('street').value=("");
        document.getElementById('district').value=("");
        document.getElementById('city').value=("");
        document.getElementById('uf').selectedIndex = 0;
      }
      function selectUF(compara) {
        let sel = document.getElementById('uf');
        let count = sel.options.length;
        for (i = 0; i < count; i++) {
          if (sel.options[i].value == compara) {
            sel.selectedIndex = i; 
            return;
          }
        }   
      }
      function myCallback(conteudo) {
        if (!("erro" in conteudo)) {
          document.getElementById('street').value=(conteudo.logradouro);
          document.getElementById('district').value=(conteudo.bairro);
          document.getElementById('city').value=(conteudo.localidade);
          selectUF(conteudo.uf);
        } else {
          clearCEP();
        }
      }
      function findCEP(valor) {
        var cep = valor.replace(/\D/g, '');
        if (cep != "") {
          var validacep = /^[0-9]{8}$/;
          if(validacep.test(cep)) {
            document.getElementById('street').value="...";
            document.getElementById('district').value="...";
            document.getElementById('city').value="...";
            document.getElementById('uf').selectedIndex = 0;
            var script = document.createElement('script');
            script.src = 'https://viacep.com.br/ws/'+ cep + '/json/?callback=myCallback';
            document.body.appendChild(script);
          } else {
            clearCEP();
          }
        } else {
          clearCEP();
        }
      }
    </script>
    <div class="d-flex justify-content-center">
      <form style= "width:400px;" action="?update=address&code=<?=$code_address?>&code_contact=<?=$code_contact?>" method="post" enctype="multipart/form-data">
        <h2 class="mb-3 mt-3"><pre><?= isset($_GET['code']) ? 'Alterar' : 'Incluir' ?> Endereço:<span style="color:gray;">&nbsp;#<?=$code_address?></span></pre></h2>
        <div class="mb-3">
          <label for="cep" class="form-label">CEP</label>
          <input type="text" class="form-control" name="cep" id="cep" required maxlength="8" value="<?= $cep ?>" onchange=findCEP(this.value);>
        </div>
        <div class="mb-3">
          <label for="street" class="form-label">Rua</label>
          <input type="text" class="form-control" name="street" id="street" required maxlength="80" value="<?= $street ?>">
        </div>
        <div class = "row">
          <div class="mb-3 col-md-4">
            <label for="number" class="form-label">Número</label>
            <input type="text" class="form-control" name="number" id="number" required maxlength="5" value="<?= $number ?>">
          </div>           
          <div class="mb-3 col-md-8">
            <label for="complement" class="form-label">Complemento</label>
            <input type="text" class="form-control" name="complement" id="complement" maxlength="20" value="<?= $complement ?>">
          </div>
        </div>
        <div class="mb-3">
          <label for="district" class="form-label">Bairro</label>
          <input type="text" class="form-control" name="district" id="district" required maxlength="30" value="<?= $district ?>">
        </div>
        <div class = "row">
          <div class="mb-3 col-md-9">
            <label for="city" class="form-label">Cidade</label>
            <input type="text" class="form-control" name="city" id="city" required maxlength="30" value="<?= $city ?>">
          </div>
          <div class="mb-3 col-md-3">
            <?php
              $uf_options = array();
              $sql = "SHOW COLUMNS FROM enderecos LIKE 'uf'";
              $result = mysqli_query($connection, $sql);
              if ($result) {
                $rows = mysqli_fetch_array($result, MYSQLI_ASSOC);
                if ($rows) {  
                  $type = $rows['Type'];
                  preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
                  $uf_options = explode("','", $matches[1]);
                }
              }
            ?>
            <label for="uf" class="form-label">UF</label>
            <select class="form-control" name="uf" id="uf">
              <?php foreach ($uf_options as &$valor) : ?> 
                <option value="<?= $valor ?>" <?= $uf == $valor ? 'selected' : '' ?>><?= $valor ?></option>
              <?php endforeach ; ?>
            </select>             
          </div>
        </div>
        <div class="d-flex justify-content-center" style="width:400px;">
          <button type="button" class="col-md-5 btn btn-secondary" onclick=<?= isset($_GET['code']) ? "window.location.href='" .$_SERVER['PHP_SELF'] . "'" : "window.location.href='?delete=address&code=" . $code_address . "'" ?>>Cancelar</button>
          <div class="col-md-2">&nbsp;&nbsp;</div>
          <button type="submit" class="col-md-5 btn btn-primary">Gravar</button>
        </div>  
      </form>
    </div>
  <?php elseif (isset($_GET['insert']) && $_GET['insert'] == "phone") : ?>
    <!-- Incluir Telefone -->
    <div class="d-flex justify-content-center">
      <form class="row" style= "width:400px;" action="?update=phone&code=<?=$code_phone?>&code_contact=<?=$code_contact?>" method="post" enctype="multipart/form-data">
        <h2 class="mb-3 mt-3"><pre><?= isset($_GET['code']) ? 'Alterar' : 'Incluir' ?> Telefone:<span style="color:gray;">&nbsp;#<?=$code_phone?></span></pre></h2>
        <div class="mb-3 col-md-4">
          <label for="ddd" class="form-label">DDD</label>
          <input type="text" class="form-control" name="ddd" id="ddd" required maxlength="4" value="<?= $ddd ?>">
        </div>
        <div class="mb-3 col-md-4">
          <label for="number" class="form-label">Número</label>
          <input type="text" class="form-control" name="number" id="number" required maxlength="9" value="<?= $number ?>">
        </div>
         <div class="mb-3 col-md-4">
          <label for="type" class="form-label">Tipo</label>
          <select class="form-control" name="type" required>
            <option value="Celular" <?= $type == "Celular" ? 'selected' : '' ?>>Celular</option>
            <option value="Fixo" <?= $type == "Fixo" ? 'selected' : '' ?>>Fixo</option>
            <option value="WhatsApp" <?= $type == "WhatsApp" ? 'selected' : '' ?>>WhatsApp</option>
          </select>
        </div>
        <div class="d-flex justify-content-center" style="width:400px;">
          <button type="button" class="col-md-5 btn btn-secondary" onclick=<?= isset($_GET['code']) ? "window.location.href='" .$_SERVER['PHP_SELF'] . "'" : "window.location.href='?delete=phone&code=" . $code_phone . "'" ?>>Cancelar</button>
          <div class="col-md-2">&nbsp;&nbsp;</div>
          <button type="submit" class="col-md-5 btn btn-primary">Gravar</button>
        </div>  
      </form>
    </div>
  <?php elseif (isset($_GET['update']) && $_GET['update'] == "contact") : ?>
    <!-- Alterar Contato -->
    <div class="container">
      <div class="row">
        <div class="col-xs-12 board">Contato&nbsp;<span style="color:lightGray;">#<?= $code_contact ?></span><br />Alterado com êxito!</div>   
      </div>
    </div>
   <script>
     setTimeout(() => location.href="<?= $_SERVER['PHP_SELF'] ?>", 2000);
   </script>
  <?php elseif (isset($_GET['update']) && $_GET['update'] == "address") : ?>
    <!-- Alterar Endereço -->
    <div class="container">
      <div class="row">
        <div class="col-xs-12 board">Endereço&nbsp;<span style="color:lightGray;">#<?= $code_address ?></span><br />Alterado com êxito!</div>   
      </div>
    </div>
   <script>
     setTimeout(() => location.href="<?= $_SERVER['PHP_SELF'] ?>?insert=contact&code=<?= $code_contact ?>", 2000);
   </script>
  <?php elseif (isset($_GET['update']) && $_GET['update'] == "phone") : ?>
    <!-- Alterar Telefone -->
    <div class="container">
      <div class="row">
        <div class="col-xs-12 board">Telefone&nbsp;<span style="color:lightGray;">#<?= $code_phone ?></span><br />Alterado com êxito!</div>   
      </div>
    </div>
   <script>
     setTimeout(() => location.href="<?= $_SERVER['PHP_SELF'] ?>?insert=contact&code=<?= $code_contact ?>", 2000);
   </script>
  <?php elseif (isset($_GET['delete']) && $_GET['delete'] == "contact") : ?>
    <!-- Remover Contato -->
    <div class="container">
      <div class="row">
        <div class="col-xs-12 board">Contato&nbsp;<span style="color:lightGray;">#<?= $code_contact ?></span><br />Removido com êxito!</div>   
      </div>
    </div>
    <script>
      setTimeout(() => location.href="<?= $_SERVER['PHP_SELF'] ?>", 2000);
    </script>
  <?php elseif (isset($_GET['delete']) && $_GET['delete'] == "address") : ?>
    <!-- Remover Endereço -->
    <div class="container">
      <div class="row">
        <div class="col-xs-12 board">Endereço&nbsp;<span style="color:lightGray;">#<?= $code_address ?></span><br />Removido com êxito!</div>   
      </div>
    </div>
    <script>
      setTimeout(() => location.href="<?= $_SERVER['PHP_SELF'] ?>?insert=contact&code=<?= $code_contact ?>", 2000);
    </script>
  <?php elseif (isset($_GET['delete']) && $_GET['delete'] == "phone") : ?>
    <!-- Remover Telefone -->
    <div class="container">
      <div class="row">
        <div class="col-xs-12 board">Telefone&nbsp;<span style="color:lightGray;">#<?= $code_phone ?></span><br />Removido com êxito!</div>   
      </div>
    </div>
    <script>
      setTimeout(() => location.href="<?= $_SERVER['PHP_SELF'] ?>?insert=contact&code=<?= $code_contact ?>", 2000);
    </script>
  <?php else : ?>
    <!-- Listar Contatos -->
    <script>
      document.cookie.split(";").forEach(function(c) { document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/"); });
    </script> 
    <div class="d-flex justify-content-center mt-3">
      <div style= "width:400px;">
        <fieldset class="mb-3">
          <legend>
            Contatos
          </legend>
          <div class="table-responsive-md">
            <table class="table">
              <tr>
                <th>Nome</th>
                <th>Sobrenome</th>
                <th></th>
              </tr>
              <?php foreach ($rows as &$valor) : ?> 
                <tr>
                  <td><?= $valor['nome'] ?></td>
                  <td><?= $valor['sobrenome'] ?></td>
                  <td nowrap>
                    <a href="?insert=contact&code=<?= $valor['codigo'] ?>" title="atualizar"><i style="color:green;" class="fa-solid fa-pen-to-square"></i></a>
                    &nbsp;
                    <a href="?delete=contact&code=<?= $valor['codigo'] ?>" title="remover"><i style="color:red;" class="fa-solid fa-trash"></i></a>
                  </td>
                </tr>
              <?php endforeach ; ?>
              <tr>
                <td style="text-align:center;" colspan="3">
                  <button type="button" class="btn btn-primary btn-lg" onclick="window.location.href='?insert=contact';">Incluir</button>
                </td>
              </tr>
            </table>
          </div>  
        </fieldset>
  <?php endif ; ?>
  </body>
</html>  
