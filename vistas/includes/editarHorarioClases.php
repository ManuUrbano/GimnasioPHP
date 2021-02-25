<div class="container-fluid px-5">
    <h2 class="text-center m-3">Edición de activiades.</h2>

    <?php if (isset($mensajes)) {
        foreach ($mensajes as $mensaje) : ?>
            <div class="alert alert-<?= $mensaje["tipo"] ?>"><?= $mensaje["mensaje"] ?></div>
    <?php endforeach;
    } ?>

    <table class="table table-sm table-striped table-bordered table-dark table-hover">

        <thead class="text-center">
            <tr>
                <th>Hora</th>
                <th>Lunes</th>
                <th>Martes</th>
                <th>Miercoles</th>
                <th>Jueves</th>
                <th>Viernes</th>
                <th>Sabado</th>
            </tr>
        </thead>

        <tbody class="text-center">
            <?php
            $contador = 0;
            $vacio = true;
            foreach ($horario as $d) : ?>

                <!--Mostramos cada registro en una fila de la tabla-->
                <tr>
                    <td>
                        <?= $d ?>
                    </td>
                    <td>

                        <?php foreach ($datos as $clase) : ?>
                            <?php if ($clase["Dia"] == 'Lunes' && $clase['horaInicio'] == $d || $clase["Dia"] == 'Lunes' &&  $clase['horaFin'] > $d && $clase['horaInicio'] < $d || $clase["Dia"] == 'Lunes' &&  $clase['horaFin'] == $d) : ?>
                                <?= $clase['nombre'] ?><br>
                                <a href="?controller=index&accion=delClaseExistente&id=<?= $clase['id'] ?>">Eliminar</a><br>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </td>


                    <td>
                        <?php foreach ($datos as $clase) : ?>
                            <?php if ($clase["Dia"] == 'Martes' && $clase['horaInicio'] == $d || $clase["Dia"] == 'Martes' &&  $clase['horaFin'] > $d && $clase['horaInicio'] < $d || $clase["Dia"] == 'Martes' &&  $clase['horaFin'] == $d) : ?>
                                <?= $clase['nombre'] ?><br>
                                <a href="?controller=index&accion=delClaseExistente&id=<?= $clase['id'] ?>">Eliminar</a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </td>


                    <td>
                        <?php foreach ($datos as $clase) : ?>
                            <?php if ($clase["Dia"] == 'Miercoles' && $clase['horaInicio'] == $d || $clase["Dia"] == 'Miercoles' &&  $clase['horaFin'] > $d && $clase['horaInicio'] < $d || $clase["Dia"] == 'Miercoles' &&  $clase['horaFin'] == $d) : ?>
                                <?= $clase['nombre'] ?><br>
                                <a href="?controller=index&accion=delClaseExistente&id=<?= $clase['id'] ?>">Eliminar</a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </td>

                    <td>
                        <?php foreach ($datos as $clase) : ?>
                            <?php if ($clase["Dia"] == 'Jueves' && $clase['horaInicio'] == $d || $clase["Dia"] == 'Jueves' &&  $clase['horaFin'] > $d && $clase['horaInicio'] < $d || $clase["Dia"] == 'Jueves' &&  $clase['horaFin'] == $d) : ?>
                                <?= $clase['nombre'] ?><br>
                                <a href="?controller=index&accion=delClaseExistente&id=<?= $clase['id'] ?>">Eliminar</a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </td>

                    <td>
                        <?php foreach ($datos as $clase) : ?>
                            <?php if ($clase["Dia"] == 'Viernes' && $clase['horaInicio'] == $d || $clase["Dia"] == 'Viernes' &&  $clase['horaFin'] > $d && $clase['horaInicio'] < $d || $clase["Dia"] == 'Viernes' &&  $clase['horaFin'] == $d) : ?>
                                <?= $clase['nombre'] ?><br>
                                <a href="?controller=index&accion=delClaseExistente&id=<?= $clase['id'] ?>">Eliminar</a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </td>

                    <td>
                        <?php foreach ($datos as $clase) : ?>
                            <?php if ($clase["Dia"] == 'Sabado' && $clase['horaInicio'] == $d || $clase["Dia"] == 'Sabado' &&  $clase['horaFin'] > $d && $clase['horaInicio'] < $d || $clase["Dia"] == 'Sabado' &&  $clase['horaFin'] == $d) : ?>
                                <?= $clase['nombre'] ?><br>
                                <a href="?controller=index&accion=delClaseExistente&id=<?= $clase['id'] ?>">Eliminar</a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </td>

                </tr>

            <?php $contador++;
            endforeach; ?>

        </tbody>

    </table>

</div>