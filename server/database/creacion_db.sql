DROP DATABASE IF EXISTS bibliopocketDB;
CREATE DATABASE bibliopocketDB CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE bibliopocketDB;

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET COLLATION_CONNECTION = utf8mb4_unicode_ci;

/* CREACIÓN ENTIDADES */
CREATE TABLE admins (
  id_admin VARCHAR(128) PRIMARY KEY NOT NULL,
  nombre_admin VARCHAR(128) UNIQUE NOT NULL,
  contrasenha_admin VARCHAR(70) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE usuarios (
  id VARCHAR(128) PRIMARY KEY NOT NULL,
  nombre_usuario VARCHAR(128) UNIQUE NOT NULL,
  contrasenha_usuario VARCHAR(70) NOT NULL,
  email_usuario VARCHAR(256) UNIQUE NOT NULL,
  user_pic VARCHAR(128),
  fecha_registro DATETIME NOT NULL DEFAULT NOW(),
  fecha_ultimo_login DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/* Tabla que permitirá gestionar los permisos vinculados a una persona usuaria */
CREATE TABLE roles (
  id VARCHAR(128) PRIMARY KEY NOT NULL,
  id_usuario VARCHAR(128) UNIQUE NOT NULL,
  p_anhadir_libros BOOLEAN,
  p_consultar_api_externa BOOLEAN
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/* Tabla para almacenar contraseñas temporales cuando la persona usuaria olvida su contraseña */
CREATE TABLE contrasenhas_temporales (
  id VARCHAR(128) PRIMARY KEY NOT NULL,
  email_usuario VARCHAR(256) NOT NULL,
  contrasenha_temporal VARCHAR(70) NOT NULL,
  fecha_expiracion TIMESTAMP NOT NULL,
  CONSTRAINT fk_contrasenhasTemporalesUsuarios FOREIGN KEY (email_usuario) REFERENCES usuarios(email_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/* Tabla para llevar un registro de los bloqueos temporales ejecutados contra una persona usuaria */
CREATE TABLE bloqueos (
  id VARCHAR(128) PRIMARY KEY NOT NULL,
  tipo INT UNIQUE NOT NULL
  -- TODO: descripcion VARCHAR(256) NOT NULL
  -- TODO: nivel_gravedad INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE bloqueos_usuarios (
  id VARCHAR(128) PRIMARY KEY NOT NULL,
  id_bloqueo VARCHAR(128) NOT NULL,
  id_usuario VARCHAR(128) NOT NULL,
  fecha_expiracion TIMESTAMP NOT NULL,
  CONSTRAINT fk_BloqueosUsuarios_Usuarios FOREIGN KEY (id_usuario) REFERENCES usuarios(id),
  CONSTRAINT fk_BloqueosUsuarios_Bloqueos FOREIGN KEY (id_bloqueo) REFERENCES bloqueos(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE libros (
  id VARCHAR(128) PRIMARY KEY NOT NULL,
  titulo VARCHAR(256) NOT NULL,
  subtitulo VARCHAR(256),
  autoria VARCHAR(256),
  descripcion VARCHAR(5000),
  portada VARCHAR(512),
  num_paginas INTEGER,
  editorial VARCHAR(128),
  anho_publicacion VARCHAR(4),
  enlace_API VARCHAR(256),
  estado INT, -- 0: "Pendiente" | 1: "Leyendo" | 2: "Leído"
  fecha_adicion DATETIME NOT NULL,
  id_usuario VARCHAR(128) NOT NULL,
  CONSTRAINT fk_LibrosUsuarios FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 'Categorías' como atributo multivaluado de 'Libros'
CREATE TABLE categorias (
  id VARCHAR(128) PRIMARY KEY NOT NULL,
  nombre VARCHAR(256) NOT NULL,
  fecha_adicion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  id_libro VARCHAR(128) NOT NULL,
  CONSTRAINT fk_CategoriasLibros FOREIGN KEY (id_libro) REFERENCES libros(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE valoraciones (
  id VARCHAR(128) PRIMARY KEY NOT NULL,
  val_numerica INT NOT NULL,
  resenha VARCHAR(2000),
  fecha_emision DATETIME NOT NULL,
  id_usuario VARCHAR(128) NOT NULL,
  CONSTRAINT fk_ValoracionesUsuarios FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Creación de admin para demo
INSERT INTO admins VALUES(
  UUID(),
  "adminBPtesting",
  "$2y$10$eJbGd/sAG6v7sMn6kM7g/Oaxg6qkjCzjWt.GMG7qiR1lM4jR.iMXC"
);

-- Creación de usuario para demo
INSERT INTO `usuarios` (`id`, `nombre_usuario`, `contrasenha_usuario`, `email_usuario`, `user_pic`, `fecha_registro`, `fecha_ultimo_login`) VALUES
('28e62-7e7a0-71e59-2f594', 'testing-libros', '$2y$10$5V2boUo3wW8b6Yxcmz00J.islGaBFr8ox9uWsxQWLV2h89ogQwu6G', 'test-libros@test.test', NULL, '2024-05-30 14:28:05', '2024-05-30 14:43:21'),
('d02b7-aa8c6-73724-cddf7', 'testing', '$2y$10$X6E8.2fofYExDpAVmzDrzeEAeKdfCCXMizPGOnyYeRk24vmlh1ksG', 'test@test.test', NULL, '2024-05-30 12:23:19', '2024-05-30 12:23:19');

-- Se le asocian los permisos por defecto
INSERT INTO `roles` (`id`, `id_usuario`, `p_anhadir_libros`, `p_consultar_api_externa`) VALUES
('a2cee281-1e6e-11ef-866f-0242ac120003', 'd02b7-aa8c6-73724-cddf7', 1, 1),
('bc0bd-4f318-fadff-84639', '28e62-7e7a0-71e59-2f594', 1, 1);

-- Creación de estantería para uno de los usuarios de la demo
INSERT INTO `libros` (`id`, `titulo`, `subtitulo`, `autoria`, `descripcion`, `portada`, `num_paginas`, `editorial`, `anho_publicacion`, `enlace_API`, `estado`, `fecha_adicion`, `id_usuario`) VALUES
('2zTpEAAAQBAJ-44f7', 'Pensar é escuro', '', 'Luisa Villalta', 'Esta obra recupera os catro títulos que Luísa Villalta dispuxo en vida para a súa publicación: Música reservada (1991), Ruído (1995), Rota ao interior do ollo (1995) e En concreto (2004). Estes versos converteron a autora nunha das voces máis importantes da poesía galega do noso tempo e consagrárona como unha creadora singular e inimitable.', 'http://books.google.com/books/content?id=2zTpEAAAQBAJ&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api', 336, 'Editorial Galaxia', '2023', 'https://play.google.com/store/books/details?id=2zTpEAAAQBAJ&source=gbs_api', 0, '2024-05-30 14:32:13', '28e62-7e7a0-71e59-2f594'),
('57lImFKiAtsC-160b', 'Inés y la alegría', '', 'Almudena Grandes', 'Toulouse, verano de 1939. Carmen de Pedro, responsable en Francia de los diezmados comunistas españoles, se cruza con Jesús Monzón, un ex cargo del partido que, sin ella intuirlo, alberga un ambicioso plan. Meses más tarde, Monzón, convertido en su pareja, ha organizado el grupo más disciplinado de la Resistencia contra la ocupación alemana, prepara la plataforma de la Unión Nacional Española y cuenta con un ejército de hombres dispuestos a invadir España. Entre ellos está Galán, que ha combatido en la Agrupación de Guerrilleros Españoles y que cree, como muchos otros en el otoño de 1944, que tras el desembarco aliado y la retirada de los alemanes, es posible establecer un gobierno republicano en Viella. No muy lejos de allí, Inés vive recluida y vigilada en casa de su hermano, delegado provincial de Falange en Lérida. Ha sufrido todas las calamidades desde que, sola en Madrid, apoyó la causa republicana durante la guerra, pero ahora, cuando oye a escondidas el anuncio de la operación Reconquista de España en Radio Pirenaica, Inés se arma de valor, y de secreta alegría, para dejar atrás los peores años de su vida.', 'http://books.google.com/books/content?id=57lImFKiAtsC&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api', 748, 'Grupo Planeta Spain', '2012', 'https://play.google.com/store/books/details?id=57lImFKiAtsC&source=gbs_api', 0, '2024-05-30 14:41:02', '28e62-7e7a0-71e59-2f594'),
('Am9sCgAAQBAJ-905c', 'Ciencia política', 'Un manual', 'Josep Ma Vallès | Salvador Martí Puig', 'La política se presenta a menudo como la acumulación desordenada y confusa de palabras y acciones. Por este motivo, puede llamar la atención el intento de asociar política y ciencia. ¿Hasta qué punto cabe un saber ordenado sobre los fenómenos políticos? ¿Sobre qué conocimientos se construye este saber? Al mismo tiempo, podemos preguntarnos si la política sigue siendo una actividad relevante cuando la globalización económica o la expansión de las redes sociales parecen imponer reglas invisibles y elaboradas al margen de los procesos políticos de carácter institucional. A estos y otros interrogantes intenta responder esta obra. A modo de introducción a la disciplina, expone sus logros y sus limitaciones. Deja abiertas muchas preguntas, pero también proporciona datos y claves para explicar hechos y, por tanto, para ayudar a quienes quieren influir sobre ellos. En esta nueva edición totalmente actualizada y puesta al día, los autores se dirigen a estudiantes que aspiran a una formación universitaria en ciencia política, derecho público y constitucional, gestión y administración pública, economía o comunicación social. Les facilita referencias y ejemplos para que construyan su propia idea de la política.', 'http://books.google.com/books/content?id=Am9sCgAAQBAJ&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api', 682, 'Grupo Planeta Spain', '2015', 'https://play.google.com/store/books/details?id=Am9sCgAAQBAJ&source=gbs_api', 1, '2024-05-30 14:32:41', '28e62-7e7a0-71e59-2f594'),
('AZnqDwAAQBAJ-d36c', 'Senlleiras', '', 'Antía Yáñez Rodríguez', 'PREMIO DE NARRATIVA ILLA NOVA 2018 A novela das mulleres que rachan co silencio. Unha influencer sobe unha foto ás redes sociais onde aparece unha man fantasma. Xusto unha semana máis tarde, a moza é atopada morta. A partir deste fío técense as vidas de varias mulleres de diferentes séculos. Mulleres que tiveron que vivir as súas vidas supeditadas ás dos seus homes, baixo as estritas normas non escritas (ou si) que as sinalaban como inferiores. Mulleres que, cando intentaron recuperar o único que lles pertencía por dereito, a súa vida, viron como ata esta lles podía ser arrebatada. Tempos que semellan totalmente diferentes, pero que non o son tanto. Mulleres senlleiras que merecen ser lembradas.', 'http://books.google.com/books/content?id=AZnqDwAAQBAJ&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api', 239, 'Editorial Galaxia', '2019', 'https://play.google.com/store/books/details?id=AZnqDwAAQBAJ&source=gbs_api', 2, '2024-05-30 14:37:02', '28e62-7e7a0-71e59-2f594'),
('B6ndDwAAQBAJ-0507', 'El universo en tu mano', 'Un viaje extraordinario a los límites del tiempo y el espacio', 'Christophe Galfard', 'No estás solo en el universo. Y no estás solo en este viaje por el universo. Estás tumbado mirando el cielo en una playa cuando alguien te coge de la mano. Te guía en una odisea alucinante hasta los agujeros negros, las galaxias más lejanas y el inicio mismo del cosmos. Abandonas tu cuerpo y te desplazas a velocidades imposibles, te introduces en un núcleo atómico, viajas en el tiempo, entras en el Sol. No es que te expliquen el universo. Es que lo tocas. No es que por fin entiendas el universo. Lo tienes en tu mano. **** Christophe Galfard, el mejor discípulo de Stephen Hawking, es uno de los divulgadores científicos más renombrados del planeta. \"El universo en tu mano\" ha recibido el premio al mejor libro de ciencia de 2015 en Francia, donde lleva vendidos más de 50.000 ejemplares.', 'http://books.google.com/books/content?id=B6ndDwAAQBAJ&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api', 480, 'Blackie Books', '2020', 'https://play.google.com/store/books/details?id=B6ndDwAAQBAJ&source=gbs_api', 2, '2024-05-30 14:35:58', '28e62-7e7a0-71e59-2f594'),
('BDHCCgAAQBAJ-9a84', 'En defensa del error', 'Un ensayo sobre el arte de equivocarse', 'Kathryn Schulz', '«Una disertación divertida y filosófica sobre por qué el error es nuestro rasgo más humano, valiente y atractivo».The New York Times «Un manifiesto nuevo y brillante que nos alienta a reconciliarnos con nuestros propios errores».The Independent Con una elocuencia y un humor extraordinarios, la periodista Kathryn Schulz investiga en En defensa del error, los motivos de que nos resulte tan gratificante estar en lo cierto y tan irritante equivocarnos, y cómo esta actitud erosiona nuestras relaciones, bien con familiares, compañeros de trabajo, vecinos e incluso otros países. Al mismo tiempo, nos invita a realizar un recorrido fascinante por la falibilidad humana, desde las convicciones erróneas a los divorcios, de los fallos médicos a las catástrofes marítimas, de las profecías fallidas a los falsos recuerdos, del «¡te lo dije!» a «hemos cometido errores».La autora recurre a pensadores tan diversos como San Agustín, Darwin, Freud, Gertrude Stein, Alan Greenspan o Groucho Marx para proponer una nueva forma de entender las equivocaciones. A su entender, el error es a la vez un don y algo adquirido, algo que puede transformar nuestra visión del mundo, nuestras relaciones y, a un nivel más profundo, a nosotros mismos.', 'http://books.google.com/books/content?id=BDHCCgAAQBAJ&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api', 393, 'Siruela', '2015', 'https://play.google.com/store/books/details?id=BDHCCgAAQBAJ&source=gbs_api', 2, '2024-05-30 14:30:02', '28e62-7e7a0-71e59-2f594'),
('InWIifBv-PcC-be61', 'Os dous de sempre', '', 'Castelao', 'Esta novela, publicada por primeira vez en 1934, converteuse, desde a súa recuperación por Galaxia en 1967, nun dos libros preferidos polos nosos lectores, que fixeron del unha das obras de máis difusión de toda a historia da literatura galega. É a historia de dous tipos universais, Pedriño e Rañolas, que encarnan dúas maneiras de ser de sempre. O primeiro, que ve o mundo desde a perspectiva da ra, sería quen de comer o seu pai polos pés. O segundo, que se fose rico andaría nun cabalo branco, ve o mundo desde a perspectiva alta do paxaro. Os dous de sempre é a única novela de Castelao.', 'http://books.google.com/books/content?id=InWIifBv-PcC&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api', 258, 'Editorial Galaxia', '2004', 'http://books.google.es/books?id=InWIifBv-PcC&dq=os+dous+de+sempre&hl=&source=gbs_api', 1, '2024-05-30 14:32:20', '28e62-7e7a0-71e59-2f594'),
('ksso3CyUsqsC-6a0a', 'Historia de la filosofía occidental II', '', 'Bertrand Russell', 'En este segundo tomo, Bertrand Russell completa el análisis de la filosofía medieval con el estudio de los escolásticos y traza una panorámica en profundidad de la filosofía renacentista y la trayectoria de los principales filósofos del mundo moderno desde la Reforma protestante hasta el siglo XX.', 'http://books.google.com/books/content?id=ksso3CyUsqsC&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api', 560, 'Grupo Planeta Spain', '2013', 'https://play.google.com/store/books/details?id=ksso3CyUsqsC&source=gbs_api', 1, '2024-05-30 14:30:39', '28e62-7e7a0-71e59-2f594'),
('lDMIMouE7WMC-c49e', 'Historia de la filosofía occidental I', '', 'Bertrand Russell', 'Bertrand Russell se guió en esta obra por el más ajustado sentido de la unidad histórica y estudió a cada filósofo en relación con el medio en que actuó, teniendo siempre en cuenta las circunstancias sociales y políticas de su época. En este primer volumen se analizan la filosofía presocrática, las aportaciones de Sócrates, Platón y Aristóteles, la filosofía helenística y a los Padres de la primera filosofía católica. Jesús Mosterín analiza en su Prólogo la trayectoria biográfica y la evolución del pensamiento de este gran filósofo: su rebelión contra el idealismo vigente; el desarrollo del logicismo aplicado a las matemáticas y al conocimiento empírico; su faceta de filósofo práctico y su trabajo en la historia de la filosofía que tendrá como resultado esta obra.', 'http://books.google.com/books/content?id=lDMIMouE7WMC&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api', 720, 'Grupo Planeta Spain', '2013', 'https://play.google.com/store/books/details?id=lDMIMouE7WMC&source=gbs_api', 2, '2024-05-30 14:30:29', '28e62-7e7a0-71e59-2f594'),
('MxJ1EAAAQBAJ-9420', 'Fantasmas azules', '', 'Farias, Paula', 'Tras la ruptura amorosa, María va a Kabul, capital de Afganistán, como corresponsal; ahí escribe historias de la vida cotidiana para un periódico. Al igual que las demás mujeres, lleva puestos “los velos azules” que la integran al mundo de lo invisible en un mundo dominado completamente por los hombres. María pretende pasar desapercibida como las mujeres de ese país; sin embargo, su tez blanca la hace notable, la distingue y la vuelve más etérea. Ulises siente la necesidad de reencontrarse con María y pide a sus amigos emprender su búsqueda. Simón, Mahmud e Ibrahím participan en la misión, pero es Simón quien busca a María con una intención que sobrepasa la encomienda original, con razones del corazón. María se ríe del amor, por eso, escudada en los velos azules, está segura de que nadie volverá a hacerle daño.', 'http://books.google.com/books/content?id=MxJ1EAAAQBAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api', 133, 'Alianza de Novelas', '2022', 'http://books.google.es/books?id=MxJ1EAAAQBAJ&dq=fantasmas+azules+paula&hl=&source=gbs_api', 2, '2024-05-30 14:32:57', '28e62-7e7a0-71e59-2f594'),
('QBwnDwAAQBAJ-7e8c', 'Cien años de soledad (edición ilustrada)', '', 'Gabriel García Márquez | Luisa Rivera', 'En ocasión del 50 aniversario de la publicación de Cien años de soledad, llega una edición con ilustraciones inéditas de la artista chilena Luisa Rivera y con una tipografía creada por el hijo del autor, Gonzalo García Barcha. Una edición conmemorativa de una novela clave en la historia de la literatura, una obra que todos deberíamos tener en nuestras estanterías. «Muchos años después, frente al pelotón de fusilamiento, el coronel Aureliano Buendía había de recordar aquella tarde remota en que su padre lo llevó a conocer el hielo.» Con esta cita comienza una de las novelas más importantes del siglo XX y una de las aventuras literarias más fascinantes de todos los tiempos. Millones de ejemplares de Cien años de soledad leídos en todas las lenguas y el premio Nobel de Literatura coronando una obra que se había abierto paso «boca a boca» -como gustaba decir el escritor- son la más palpable demostración de que la aventura fabulosa de la familia Buendía-Iguarán, con sus milagros, fantasías, obsesiones, tragedias, incestos, adulterios, rebeldías, descubrimientos y condenas, representaba al mismo tiempo el mito y la historia, la tragedia y el amor del mundo entero. El mejor homenaje a Gabo es leerlo. Pablo Neruda dijo... «El Quijote de nuestro tiempo.»', 'http://books.google.com/books/content?id=QBwnDwAAQBAJ&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api', 531, 'RANDOM HOUSE', '2017', 'https://play.google.com/store/books/details?id=QBwnDwAAQBAJ&source=gbs_api', 0, '2024-05-30 14:33:11', '28e62-7e7a0-71e59-2f594'),
('QftctAEACAAJ-3356', 'La mujer que mira a los hombres que miran a las mujeres', '', 'Siri Hustvedt', '«La mujer que mira a los hombres que miran a las mujeres». Así define Siri Hustvedt esta ambiciosa reunión de sus mejores ensayos, escritos entre 2011 y 2015. Su vasto conocimiento en un amplio abanico de disciplinas como el arte, la literatura, la neurociencia o el psicoanálisis ilumina una teoría central en su obra ensayística, la de que la percepción está influenciada por nuestros prejuicios cognitivos implícitos, aquellos que no provienen del entorno, sino que se han interiorizado como una realidad psicofisiológica. Una apasionante y radical colección de ensayos sobre el feminismo de la galardonada escritora Siri Hustvedt.', 'http://books.google.com/books/content?id=QftctAEACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api', NULL, 'Planeta Publishing', '2017', 'http://books.google.es/books?id=QftctAEACAAJ&dq=siri+hustvedt+la+mujer&hl=&source=gbs_api', 2, '2024-05-30 14:36:32', '28e62-7e7a0-71e59-2f594'),
('xe6-DwAAQBAJ-cb3c', 'Nueve cuentos malvados', '', 'Margaret Atwood', 'Estos cuentos confirman a la autora canadiense como una incisiva cronista de nuestros impulsos más oscuros. Consagrada gracias a la fabulosa difusión de sus novelas El cuento de la criada y Alias Grace, ambas convertidas en series de éxito internacional, Margaret Atwood despliega inteligencia y humor en abundancia en estos nueve cuentos sobre las facetas más absurdas y deliciosamente malvadas del ser humano. La irrupción de vampiros, de criaturas poseídas y de espíritus, que conviven con personajes y situaciones entrañables de la vida cotidiana, muda los relatos en originalísimas variantes sobre la inagotable materia de la enfermedad, la vejez y la muerte, a la vez que suponen una tenaz defensa de valores como el derecho a la diferencia y la libertad individual, y una aguerrida vindicación de las mujeres en un entorno hostil. Así, una escritora de literatura fantástica que ha enviudado hace poco sobrevive a una tormentosa noche de invierno guiada por la voz de su difunto marido; una anciana aquejada por el síndrome de Charles Bonnet asume la presencia de enanitos imaginarios mientras una turba disfrazada con caretas se congrega ante la residencia de la tercera edad donde vive para asaltar el recinto y prenderle fuego; o una sedimentación fosilizada con mil novecientos millones de años de antigüedad venga un delito cometido tiempo atrás. La mirada cáustica, lúcida y de una gran humanidad de Atwood es el componente sustancial de una prosa implacable, un faro que no deja de iluminarnos entre tanta incertidumbre y confusión. La crítica ha dicho... «La autora reúne en este libro nueve relatos de ciencia ficción, terror gótico y thriller realista con dos denominadores comunes: el humor con que están narrados y la invitación a la tolerancia que desprenden. Las piezas narrativas muestran a viudas charlando con sus difuntos maridos, a mujeres reencontrándose con los hombres que las violaron y, entre muchas cosas más, a ancianas sacándole el máximo partido a la vejez. Especialmente notable es la ironía que destilan los relatos donde aparecen escritores. Y es que no hay nada como reírse de uno mismo.» Álvaro Colomer, El Mundo «Mordaz colección de relatos con la que la canadiense reflexiona sobre absurdos de la condición humana.» David Morán, ABC Cultural «A vueltas con la muerte, Margaret Atwood abandona a Defred y la distopía feminista pero no el territorio fantástico. Porque en estos nueve cuentos delirantemente subversivos hay vampiros que no encuentran lo que esperan, escritoras de ciencia ficción que hablan -de verdad- con sus maridos muertos y estromatolitos de 1.900 millones de años que vengan delitos del pasado. Una delicia.» Laura Fernández, Vanity Fair «Es fácil apreciar la espectacular diversidad de la obra de Margaret Atwood [...] Cuando me paro a pensarlo, y lo sumo a sus talentos literarios y sus logros, se me corta la respiración.» Alice Munro «Atwood es una poeta. Muy rara es la frase de su ágil prosa, áspera a la par que voraz, que no cumple con creces su función.» John Updike «Una de las plumas en lengua inglesa más importantes de nuestros días.» Germaine Greer «Eclécticos, divertidos, vibrantes, aterradores, bellos, una auténtica delicia.» The Boston Globe «Potentes, ingeniosos y mordaces.» The New York Times Book Review', 'http://books.google.com/books/content?id=xe6-DwAAQBAJ&printsec=frontcover&img=1&zoom=1&edge=curl&source=gbs_api', 240, 'SALAMANDRA', '2019', 'https://play.google.com/store/books/details?id=xe6-DwAAQBAJ&source=gbs_api', 0, '2024-05-30 14:33:32', '28e62-7e7a0-71e59-2f594'),
('zQZ9QwAACAAJ-b915', 'El juego lúgubre', '', 'Paco Roca', 'Descripción no disponible', 'http://books.google.com/books/content?id=zQZ9QwAACAAJ&printsec=frontcover&img=1&zoom=1&source=gbs_api', 57, 'Editorial no disponible', '2006', 'http://books.google.es/books?id=zQZ9QwAACAAJ&dq=el+faro+paco+roca&hl=&source=gbs_api', 0, '2024-05-30 14:39:51', '28e62-7e7a0-71e59-2f594');

-- Creación de categorías para algunos de los libros de la estantería
INSERT INTO `categorias` (`id`, `nombre`, `fecha_adicion`, `id_libro`) VALUES
('04a5b-e4dcb-9f016-dac7e', 'política', '2024-05-30 14:35:21', 'Am9sCgAAQBAJ-905c'),
('115c2-8bb31-7907d-277a1', 'ensayo', '2024-05-30 14:35:21', 'Am9sCgAAQBAJ-905c'),
('17ac1-0a7fe-20a4f-749a0', 'terror gótico', '2024-06-01 20:45:12', 'xe6-DwAAQBAJ-cb3c'),
('19de0-02aa5-11ca8-214cd', 'novela', '2024-05-30 14:35:14', 'MxJ1EAAAQBAJ-9420'),
('201e2-c5b51-d3209-727f7', 'psicología', '2024-06-01 20:45:34', 'QftctAEACAAJ-3356'),
('2b331-1158f-95a4f-e32ac', 'historia del arte', '2024-06-01 20:45:34', 'QftctAEACAAJ-3356'),
('30f50-8fccf-0a98e-bc19f', 'ensayo', '2024-06-01 20:45:34', 'QftctAEACAAJ-3356'),
('3b4ae-5c4ba-6c078-dc3ee', 'ensayo', '2024-06-01 20:45:23', 'B6ndDwAAQBAJ-0507'),
('46e71-6316b-40518-9ab2b', 'ensayo', '2024-05-30 14:33:59', 'lDMIMouE7WMC-c49e'),
('47cb8-43839-cf099-0be92', 'poesía', '2024-06-01 20:43:06', '2zTpEAAAQBAJ-44f7'),
('4b975-9cb76-b9ece-a751b', 'historia', '2024-05-30 14:35:08', 'ksso3CyUsqsC-6a0a'),
('6e09d-7a114-7a6e3-be05d', 'relatos cortos', '2024-06-01 20:45:12', 'xe6-DwAAQBAJ-cb3c'),
('78e33-f2a19-8319c-e35d9', 'novela', '2024-05-30 14:35:31', 'InWIifBv-PcC-be61'),
('8a167-e7d18-c9865-8d559', 'novela', '2024-06-01 20:43:14', 'QBwnDwAAQBAJ-7e8c'),
('a24e9-c85e3-6bf59-da963', 'novela histórica', '2024-06-01 20:47:34', '57lImFKiAtsC-160b'),
('ab7e5-ab585-2e08c-50523', 'ensayo', '2024-05-30 14:34:13', 'ksso3CyUsqsC-6a0a'),
('bce81-b589a-81453-9e9e2', 'historia', '2024-05-30 14:35:02', 'lDMIMouE7WMC-c49e'),
('bda2c-92dd0-8efb4-df685', 'filosofía', '2024-05-30 14:35:02', 'lDMIMouE7WMC-c49e'),
('c5ef1-7c801-74558-11413', 'ensayo', '2024-05-30 14:30:15', 'BDHCCgAAQBAJ-9a84'),
('d92db-1ac14-74493-35352', 'novela', '2024-06-01 20:45:54', 'AZnqDwAAQBAJ-d36c'),
('db3cc-efae1-282be-a5eca', 'novela gráfica', '2024-06-01 20:47:22', 'zQZ9QwAACAAJ-b915'),
('e12f5-4ef73-33a15-8a380', 'filosofía', '2024-05-30 14:34:13', 'ksso3CyUsqsC-6a0a'),
('f2b85-6cdeb-8d0c2-b1e22', 'astronomía', '2024-06-01 20:45:23', 'B6ndDwAAQBAJ-0507');

-- Creación de motivos de bloqueo para demo
INSERT INTO bloqueos VALUES
  (UUID(), 1),
  (UUID(), 2),
  (UUID(), 3),
  (UUID(), 4),
  (UUID(), 5);


-- Se activa la programación de eventos
SET GLOBAL event_scheduler = ON;

-- Para eliminar aquellas contraseñas temporales cuya fecha de expiración haya pasado
CREATE EVENT delete_contrasenhas_temporales
ON SCHEDULE EVERY 1 MINUTE
DO DELETE FROM contrasenhas_temporales WHERE fecha_expiracion <= CURRENT_TIMESTAMP();