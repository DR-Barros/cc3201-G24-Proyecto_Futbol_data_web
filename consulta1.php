<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Consulta 1</title>
        <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <link rel="stylesheet" href="consultas.css">
    </head>
<body>
    <div class="wrapper">
        <header>
            <h1> Fútbol Data Web</h1>
            <a href=".">Volver</a>
        </header>
        <div id="content" class="Consulta1">
            <form action="consulta1.php" method="get">
                <fieldset>
                    <legend> Consulta 1: </legend>
                    <h3>¿En cuáles partidos ha marcado [jugador],  cuantos goles hizo en el partido y como terminó el partido?</h3>
                    <p>Puedes ingresar parte del nombre o el nombre completo del jugador. Pero ojo que debes tener cuidado con las mayusculas y los acentos.</p>
                    <!-- Text input-->
                    <div>
                        <label> Nombre Jugador: </label>
                        <?php
                        $variable1=$_GET['nombre'];
                        echo"<input name='nombre' value=$variable1>";
                        ?>
                    </div>
    
                    <!-- Button -->
                    <div>
                        <input type="submit" value="Consultar">
                    </div>
                    <p>
                        En la tabla se muestra en la primera columna el nombre completo del jugador que marco el gol, en la segunda columna cuantos goles marco el jugador en dicho partido, 
                        en la tercera columna el nombre del equipo local y cuantos goles tuvo, en la cuarta columna el nombre del equipo visita y cuantos goles tuvo, la quinta
                        columna la liga a la que pertenece el partido y la ultima columna tiene la fecha del partido (Año-mes-dia).
                    </p>
                </fieldset>
            </form>
        </div>
        <?php
            echo "<table>";
            echo "<tr>
                    <th> Nombre </th>
                    <th> Goles </th>
                    <th> Goles Local </th>
                    <th>  </th>
                    <th> Goles Visita </th>
                    <th>  </th>
                    <th> Liga </th>
                    <th> Fecha </th>
                </tr>";

            class TableRows extends RecursiveIteratorIterator {
                function __construct($it) {
                    parent::__construct($it, self::LEAVES_ONLY);
                }
                function current() {
                    return "<td>" . parent::current(). "</td>";
                }
                function beginChildren() {
                    echo "<tr>";
                }
                function endChildren() {
                    echo "</tr>" . "\n";
                }
            }

            try {
            $pdo = new PDO('pgsql:
                            host=localhost;
                            port=5432;
                            dbname=cc3201;
                            user=webuser;
                            password=futbol2023');
            
            $stmt = $pdo->prepare("SELECT P.Jugador, P.Goles, home.name AS EquipoLocal, P.homeGoals AS GolesLocal, away.name AS EquipoVisita, P.awayGoals AS GolesVisita, liga.name AS Liga, P.date
            FROM  Futbol.NEquipo home, Futbol.NEquipo away, Futbol.Liga liga, (
                SELECT J.Jugador, J.Goles, P.gameID, P.homeGoals, P.awayGoals, P.leagueID, P.date
                FROM Futbol.Partido P, (
                    SELECT J.name AS Jugador, A.goals AS Goles, A.gameID
                    FROM Futbol.Aparece A, Futbol.Jugadores J
                    WHERE J.playerID=A.playerID
                    AND J.name LIKE :valor1
                    AND A.goals > 0 
                ) J
                WHERE J.gameID=P.gameID
            ) P
            WHERE home.gameID=P.gameID AND home.location = 'h' AND away.gameID=P.gameID AND away.location = 'a' AND liga.leagueID=P.leagueID
            ORDER BY P.date;");
            $stmt->execute(['valor1' => "%".$variable1."%"]);
            $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);

            foreach(new TableRows(new RecursiveArrayIterator($stmt->fetchAll())) as $k=>$v) {
                echo $v;
            }
            }
            catch(PDOException $e){
                echo $e->getMessage();
            }
            echo "</table>";
        ?>
    </div>
</body>
</html>
