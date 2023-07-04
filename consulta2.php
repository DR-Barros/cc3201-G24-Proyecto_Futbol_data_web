<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Consulta 2</title>
        <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <link rel="stylesheet" href="consultas.css">
    </head>
<body>
    <div class="wrapper">
        <header>
            <h1> Fútbol Data Web</h1>
            <a href=".">Volver</a>
        </header>
        <div id="content" class="Consulta2">
            <form action="consulta2.php" method="get">
                <fieldset>
                    <legend> Consulta 2:</legend>
                    <h3>¿Tabla con la información de la posición y estadísticas de equipo para una [liga], [temporada] y [fecha] específicos?</h3>
                    <p>Debes tener en consideracion que las temporadas van desde agosto del año correspondiente a la temporada hasta finales de mayo del año siguiente.</p>
                    <!-- Text input-->
                    <div>
                        <label> Ingrese liga </label>
                        <select name="liga">
                            <?php
                                $variable1=$_GET['liga'];
                                try{
                                    $pdo = new PDO('pgsql: host=localhost; port=5432; dbname=cc3201; user=webuser; password=futbol2023');
                                    $stmt = $pdo->prepare("SELECT * FROM Futbol.Liga");
                                    $stmt->execute();
                                    foreach ($stmt as $row){
                                        if ($variable1 == $row["leagueid"]) {
                                            echo "<option value='".$row["leagueid"]."' selected>".$row["name"]."</option>";
                                        } else {
                                            echo "<option value='".$row["leagueid"]."'>".$row["name"]."</option>";
                                        }
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
                            <?php
                            $variable3=$_GET['temporada'];
                            $array = ["2014","2015","2016","2017","2018","2019","2020"];
                            foreach($array as $e){
                                if ($e == $variable3) {
                                    echo "<option value='$e' selected>$e</option>";
                                } else {
                                    echo "<option value='$e'>$e</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div>
                        <label> Nombre Jugador: </label>
                        <?php
                            $variable2 = $_GET['fecha'];
                            echo "<input name='fecha' type='date' value='$variable2' min='2014-08-09' max='2021-05-24' >";
                        ?>
                    </div>
    
    
                    <!-- Button -->
                    <div>
                        <input type="submit" value="Consultar">
                    </div>
                    <div class="nomenclatura">
                        <h4>Nomenclatura:</h4>
                        <p><strong>PJ:</strong> Partidos Jugados</p>
                        <p><strong>G:</strong> Partidos Ganados</p>
                        <p><strong>E:</strong> Partidos Empatados</p>
                        <p><strong>P:</strong> Partidos Perdidos</p>
                        <p><strong>Pts:</strong> Puntos</p>
                        <p><strong>GF:</strong> Goles que a marcado el equipo</p>
                        <p><strong>xG:</strong> Goles esperados (expected goals)</p>
                        <p><strong>F:</strong> Faltas</p>
                        <p><strong>TA:</strong> Tarjetas Amarillas</p>
                        <p><strong>TR:</strong> Tarjetas Rojas</p>
                    </div>
                </fieldset>
            </form>
        </div>
        <?php
            echo "<table>";
            echo "<tr>
                    <th>Equipo</th>
                    <th>PJ</th>
                    <th>G</th>
                    <th>E</th>
                    <th>P</th>
                    <th>Pts</th>
                    <th>GF</th>
                    <th>xG</th>
                    <th>F</th>
                    <th>TA</th>
                    <th>TR</th>
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
                $variable2=strtotime($variable2);
                $variable2 = date('Y-m-d H:i:s', $variable2);
                $stmt = $pdo->prepare("SELECT e.name, COUNT(*) AS Partidos,
                SUM(CASE WHEN p.results = 'W' THEN 1 ELSE 0 END) AS Win, 
                SUM(CASE WHEN p.results = 'D' THEN 1 ELSE 0 END) AS Draw,
                SUM(CASE WHEN p.results = 'L' THEN 1 ELSE 0 END) AS Lose,
                SUM(CASE WHEN p.results = 'W' THEN 3 WHEN p.results = 'D' THEN 1 ELSE 0 END) AS Pts,
                SUM(p.goals) AS Goals, CAST(SUM(p.xGoals)AS INT) AS xGoals, SUM(p.fouls) AS Fouls,
                SUM(p.yellowCards) AS yellowCards, SUM(p.redCards) AS redCards
                FROM Futbol.Equipos e, (
                    SELECT *
                    FROM Futbol.Jugo j,(
                        SELECT gameID
                        FROM Futbol.Partido
                        WHERE leagueID = :liga
                        AND date < :fecha
                        AND season = :season
                    ) p
                    WHERE j.gameID = p.gameID
                ) p
                WHERE e.teamID = p.teamID
                GROUP BY e.name
                ORDER BY Pts DESC");
                $stmt->execute(['liga' => $variable1,'season' => $variable3, 'fecha' => $variable2]);
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
