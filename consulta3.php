<! DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Consulta 3</title>
        <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <link rel="stylesheet" href="consultas.css">
    </head>
<body>
    <div class="wrapper">
        <header>
            <h1> Fútbol Data Web</h1>
            <a href=".">Volver</a>
        </header>
        <div id="content" class="Consulta3">
            <form action="consulta3.php" method="get">
                <fieldset>
                    <legend> Consulta 3:</legend>
                    <h3>¿Conviene apostarle a tu equipo (sumar la ganancia según apuestas cuando tu equipo gana y restarle lo que invertiste si apostaras en todos los partidos)?</h3>
                    <p>Para Calcular si te conviene apostarle a tu equipo la aplicacion calcula las ganancias que se tendrian al apostar todos los partidos $1 y luego resta $1 por cada partido (gastos de apostar).</p>
                    <!-- Text input-->
                    <div>
                        <label> Nombre Equipo: </label>
                        <?php
                        $variable1=$_GET['equipo'];
                        echo"<input name='equipo' value=$variable1>";
                        ?>
                    </div>
                    <!-- Button -->
                    <div>
                        <input type="submit" value="Consultar">
                    </div>
                    <h4>Nomenclatura casas de apuestas:</h4>
                    <p><strong>B365:</strong> Bet365</p>
                    <p><strong>BW:</strong> Bet&Win</p>
                    <p><strong>IW:</strong> Interwetten</p>
                    <p><strong>PS:</strong> Pinacle</p>
                </fieldset>
            </form>
        </div>
        <?php
            echo "<table>";
            echo "<tr>
                    <th>Equipos</th>
                    <th>B365</th>
                    <th>BW</th>
                    <th>IW</th>
                    <th>PS</th>
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
                $stmt = $pdo->prepare("SELECT p.name, CAST(SUM(p.B365) AS INT) AS B365, CAST(SUM(BW) AS INT) AS BW, CAST(SUM(IW) AS INT) AS IW, CAST(SUM(PS) AS INT) AS PS  
                FROM (    
                    SELECT w.name, SUM(B365) AS B365, SUM(BW) AS BW, SUM(IW) AS IW, SUM(PS) AS PS  
                    FROM (
                        SELECT j.name, SUM(B365H) AS B365, SUM(BWH) AS BW, SUM(IWH) AS IW, SUM(PSH) AS PS  
                        FROM Futbol.Partido p, (
                            SELECT e.name, j.gameID
                            FROM Futbol.Jugo j, Futbol.Equipos e
                            WHERE j.teamID = e.teamID
                            AND e.name LIKE :valor1
                            AND j.location = 'h'
                            AND j.results = 'W'
                        ) j
                        WHERE p.gameID = j.gameID
                        GROUP BY j.name
                        UNION
                        SELECT j.name, SUM(B365A) AS B365, SUM(BWA) AS BW, SUM(IWA) AS IW, SUM(PSA) AS PS  
                        FROM Futbol.Partido p, (
                            SELECT e.name, j.gameID
                            FROM Futbol.Jugo j, Futbol.Equipos e
                            WHERE j.teamID = e.teamID
                            AND e.name LIKE :valor1
                            AND j.location = 'a'
                            AND j.results = 'W'
                        ) j
                        WHERE p.gameID = j.gameID
                        GROUP BY j.name
                    ) w
                    GROUP BY w.name
                    UNION
                    SELECT j.name, -COUNT(*) AS B365, -COUNT(*) AS BW, -COUNT(*) AS IW, -COUNT(*) AS PS  
                    FROM Futbol.Partido p, (
                        SELECT e.name, j.gameID
                        FROM Futbol.Jugo j, Futbol.Equipos e
                        WHERE j.teamID = e.teamID
                        AND e.name LIKE :valor1
                    ) j
                    WHERE p.gameID = j.gameID
                    GROUP BY j.name
                ) p
                GROUP BY p.name;");
                $stmt->execute(['valor1' => "%".$variable1."%",]);
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

