<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fútbol Data Web</title>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <div class="wrapper">
        <header>
            <h1> Fútbol Data Web</h1>
        </header>
        <div id="content" class="Consulta1">
            <form action="consulta1.php" method="get">
                <fieldset>
                    <legend> Consulta 1: </legend>
                    <h3>¿En cuáles partidos ha marcado [jugador],  cuantos goles hizo en el partido y como terminó el partido?</h3>
                    <p>Puedes ingresar parte del nombre o el nombre completo del jugador. Pero ojo que debes tener cuidado con las mayusculas y los acentos</p>
                    <!-- Text input-->
                    <div>
                        <label> Nombre Jugador: </label>
                        <input name="nombre">
                    </div>
    
                    <!-- Button -->
                    <div>
                        <input type="submit" value="Consultar">
                    </div>
    
                </fieldset>
            </form>
        </div>
        <div id="content" class="Consulta2">
            <form action="consulta2.php" method="get">
                <fieldset>
                    <legend> Consulta 2:</legend>
                    <h3>¿Tabla con la información de la posición y estadísticas de equipo para una [liga], [temporada] y [fecha] específicos?</h3>
                    <p>Debes tener en consideracion que las temporadas van desde agosto del año correspondiente a la temporada hasta finales de mayo del año siguiente</p>
                    <!-- Text input-->
                    <div>
                        <label> Ingrese liga </label>
                        <select name="liga">
                            <?php
                                try{
                                    $pdo = new PDO('pgsql: host=localhost; port=5432; dbname=cc3201; user=webuser; password=futbol2023');
                                    $stmt = $pdo->prepare("SELECT * FROM Futbol.Liga");
                                    $stmt->execute();
                                    foreach ($stmt as $row){
                                        echo "<option value='".$row["leagueid"]."'>".$row["name"]."</option>";
                                    }
                                } catch(PDOException $e){
                                    echo $e->getMessage();
                                }
                            ?>
                        </select>
                    </div>
                    <div>
                        <label> Temporada: </label>
                        <select name="temporada" >
                            <option value="2014">2014</option>
                            <option value="2015">2015</option>
                            <option value="2016">2016</option>
                            <option value="2017">2017</option>
                            <option value="2018">2018</option>
                            <option value="2019">2019</option>
                            <option value="2020">2020</option>
                        </select>
                    </div>
                    <div>
                        <label> Fecha: </label>
                        <input name="fecha" type="date" value="2021-05-24" min="2014-08-09" max="2021-05-24" >
                    </div>
    
    
                    <!-- Button -->
                    <div>
                        <input type="submit" value="Consultar">
                    </div>
    
                </fieldset>
            </form>
        </div>
        <div id="content" class="Consulta3">
            <form action="consulta3.php" method="get">
                <fieldset>
                    <legend> Consulta 3:</legend>
                    <h3>¿Conviene apostarle a tu equipo (sumar la ganancia según apuestas cuando tu equipo gana y restarle lo que invertiste si apostaras en todos los partidos)?</h3>
                    <p>Para Calcular si te conviene apostarle a tu equipo la aplicacion calcula las ganancias que se tendrian al apostar todos los partidos $1 y luego resta $1 por cada partido (gastos de apostar)</p>
                    <!-- Text input-->
                    <div>
                        <label> Nombre Equipo: </label>
                        <input name="equipo">
                    </div>
                    <!-- Button -->
                    <div>
                        <input type="submit" value="Consultar">
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</body>
</html>