<?php
// Incluimos el header público
include('includes/header_public.php');

// Obtenemos el ID del integrante desde la URL
$integrante_id = isset($_GET['id']) ? $_GET['id'] : 'default';

// Array con la información de cada integrante
$equipo = [
    'brayan' => [
        'nombres' => 'Brayan Benjamin',
        'apellidos' => 'Martínez Herrera',
        'celular' => '933234624',
        'correo' => 'brayanb.martinez@upsjb.edu.pe',
        'codigo' => '74862093',
        'imagen' => 'img/brayan.jpg', 
        'presentacion' => 'Soy Brayan Martínez, un apasionado por el desarrollo web...',
        'video_url' => 'https://www.youtube.com/embed/Ba_M7Y9GxHw?si=MDYT3jOqVtr7YjOB'
    ],

    'david' => [
        'nombres' => 'David Fernando',
        'apellidos' => 'Gamboa Juscaymayta',
        'celular' => '914620420',
        'correo' => 'david.gamboa@uspjb.edu.pe',
        'codigo' => '60725846',
        'imagen' => 'img/david.png',
        'presentacion' => '',
        'video_url' => 'https://www.youtube.com/embed/W2MpGCL8-9o?si=unGeFCsu3irTjWyZ'
    ],

    'cesar' => [
        'nombres' => 'Cesar Augusto',
        'apellidos' => 'Gavilano Falla',
        'celular' => '950152902',
        'correo' => 'cesar.gavilano@uspjb.edu.pe',
        'codigo' => '61853219',
        'imagen' => 'img/cesar.jpg',
        'presentacion' => 'Soy una persona curiosa, autodidacta...',
        'video_url' => 'https://www.youtube.com/embed/JMJP1OCPp7U?si=Z3gnU3beNL_fBu18'
    ],

    'ruben' => [
        'nombres' => 'Ruben Yholino',
        'apellidos' => 'Quintanilla Ochoa',
        'celular' => '902230142',
        'correo' => 'ruben.quintanilla@upsjb.edu.pe',
        'codigo' => '71624318',
        'imagen' => 'img/ruben.png',
        'presentacion' => 'Me gustan todas menos gordas :3',
        'video_url' => 'https://www.youtube.com/embed/PcR8P1pG2pE?si=i2XaayUjZgrG_9uu'
    ],
    
    'aaron' => [
        'nombres'      => 'Aaron Isai',
        'apellidos'    => 'Fernandez de la Cruz',
        'celular'      => '900282294',
        'correo'       => 'aaron.fernandez@upsjb.edu.pe',
        'codigo'       => '60907408',
        'imagen'       => 'img/aaron.png',
        'presentacion' => 'Hola, soy Aaron Isai Fernandez de la Cruz. Apasionado por el desarrollo web...',
        'video_url'    => 'https://www.youtube.com/embed/qNw8ejrI0nM'
    ],

    'default' => [
        'nombres' => 'Integrante',
        'apellidos' => 'del Equipo',
        'celular' => 'N/A',
        'correo' => 'N/A',
        'codigo' => 'N/A',
        'imagen' => 'img/logo.png',
        'presentacion' => 'Por favor, selecciona un integrante válido desde el pie de página.',
        'video_url' => ''
    ]
];

// Seleccionar los datos del integrante actual
$integrante_actual = isset($equipo[$integrante_id]) ? $equipo[$integrante_id] : $equipo['default'];

?>


<style>
    .presentation-container {
        max-width: 900px;
        margin: 40px auto;
        padding: 25px;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        font-family: Arial, sans-serif;
    }
    .presentation-header img {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        display: block;
        margin: 0 auto 25px;
        border: 4px solid #fff;
        box-shadow: 0 0 15px rgba(0,0,0,0.2);
    }
    .info-card {
        background-color: #f8f9fa;
        border-left: 5px solid #0056b3;
        padding: 20px;
        margin-bottom: 25px;
        border-radius: 0 8px 8px 0;
    }
    .info-card h2 {
        margin-top: 0;
        color: #333;
        font-size: 1.8em;
        text-align: center;
        border-bottom: 2px solid #eee;
        padding-bottom: 10px;
        margin-bottom: 15px;
    }
    .info-card ul { list-style: none; padding: 0; }
    .info-card ul li { font-size: 1.1em; margin-bottom: 12px; color: #555; }
    .info-card ul li strong { color: #333; width: 180px; display: inline-block; }
    .presentation-text { line-height: 1.6; font-size: 1.1em; text-align: justify; color: #444; margin-bottom: 25px;}
    .video-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; background: #000; border-radius: 8px;}
    .video-container iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%;}
</style>

<main class="content">
    <div class="presentation-container">
        
        <div class="presentation-header">
            <img src="<?php echo htmlspecialchars($integrante_actual['imagen']); ?>" alt="Foto de <?php echo htmlspecialchars($integrante_actual['nombres']); ?>">
        </div>

        <div class="info-card">
            <h2><?php echo htmlspecialchars($integrante_actual['nombres'] . ' ' . $integrante_actual['apellidos']); ?></h2>
            <ul>
                <li><strong>Código:</strong> <?php echo htmlspecialchars($integrante_actual['codigo']); ?></li>
                <li><strong>Correo:</strong> <?php echo htmlspecialchars($integrante_actual['correo']); ?></li>
                <li><strong>Celular:</strong> <?php echo htmlspecialchars($integrante_actual['celular']); ?></li>
            </ul>
        </div>

        <div class="presentation-text">
            <p><?php echo htmlspecialchars($integrante_actual['presentacion']); ?></p>
        </div>

        <?php if (!empty($integrante_actual['video_url'])): ?>
        <div class="video-container">
            <iframe 
                src="<?php echo htmlspecialchars($integrante_actual['video_url']); ?>" 
                title="Video de presentación" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                allowfullscreen>
            </iframe>
        </div>
        <?php endif; ?>

    </div>
</main>

<?php
// Incluimos el footer público
include('includes/footer.php');

?>




