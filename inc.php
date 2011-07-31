<?php
define('MORIARTY_ARC_DIR', 'lib/arc/');
define('URISPACE', 'http://metoffice.dataincubator.org/');
define('AREAS', URISPACE.'areas/');
define('DCT', 'http://purl.org/dc/terms/');
define('MET', URISPACE.'vocabs/metoffice/');
define('WEATHER', 'http://purl.org/weather#');
define('FOAF', 'http://xmlns.com/foaf/0.1/');
define('GEO', 'http://www.w3.org/2003/01/geo/wgs84_pos#');
define('OS', 'http://data.ordnancesurvey.co.uk/ontology/spatialrelations/');
define('GN', 'http://www.geonames.org/ontology#');

require_once 'lib/moriarty/credentials.class.php';
require_once 'lib/moriarty/store.class.php';
require_once 'credentials.inc.php';
$metofficeStore = new Store('http://api.talis.com/stores/metoffice', new Credentials(MetOffice_User,MetOffice_Password));

function slug($label){
  $slug = str_replace(' ', '', ucwords($label));
  $slug = str_replace('(','_', $slug);
  $slug = str_replace(')','', $slug);
  return urlencode($slug);
}

$regions = array(
    'os' => 'Orkney & Shetland',
    'he' => 'Highlands & Eilean Siar',
    'gr' => 'Grampian',
    'st' => 'Strathclyde',
    'ta' => 'Central, Tayside & Fife',
    'dg' => 'SW Scotland, Lothian Borders',
    'ni' => 'Northern Ireland',
    'wl' => 'Wales',
    'nw' => 'North West England',
    'ne' => 'North East England',
    'yh' => 'Yorkshire & Humber',
    'em' => 'East Midlands',
    'ee' => 'East of England',
    'sw' => 'South West England',
    'se' => 'London & South East England',
    );

$areas = array (
  'os/kirkwall' => 'Kirkwall',
  'os/lerwick' => 'Lerwick',
  'os/scalloway' => 'Scalloway',
  'he/aviemore' => 'Aviemore',
  'he/cromarty' => 'Cromarty',
  'he/durness' => 'Durness',
  'he/fort_augustus' => 'Fort Augustus',
  'he/fort_william' => 'Fort William',
  'he/glencoe' => 'Glencoe',
  'he/grantown-on-spey' => 'Grantown-on-Spey',
  'he/invergordon' => 'Invergordon',
  'he/inverness' => 'Inverness',
  'he/lochaline' => 'Lochaline',
  'he/mallaig' => 'Mallaig',
  'he/portree' => 'Portree',
  'he/stornoway' => 'Stornoway',
  'he/thurso' => 'Thurso',
  'he/ullapool' => 'Ullapool',
  'he/wick' => 'Wick',
  'gr/aberdeen' => 'Aberdeen',
  'gr/braemar' => 'Braemar',
  'gr/buckie' => 'Buckie',
  'gr/burghead' => 'Burghead',
  'gr/charlestown' => 'Charlestown',
  'gr/elgin' => 'Elgin',
  'gr/fraserburgh' => 'Fraserburgh',
  'gr/kinloss' => 'Kinloss',
  'gr/lossiemouth' => 'Lossiemouth',
  'gr/macduff' => 'Macduff',
  'gr/peterhead' => 'Peterhead',
  'st/ardrossan' => 'Ardrossan',
  'st/ayr' => 'Ayr',
  'st/campbeltown' => 'Campbeltown',
  'st/coll' => 'Coll',
  'st/douglas' => 'Douglas',
  'st/girvan' => 'Girvan',
  'st/glasgow' => 'Glasgow',
  'st/greenock' => 'Greenock',
  'st/hunterston' => 'Hunterston',
  'st/irvine' => 'Irvine',
  'st/kilmarnock' => 'Kilmarnock',
  'st/motherwell' => 'Motherwell',
  'st/oban' => 'Oban',
  'st/old_kilpatrick' => 'Old Kilpatrick',
  'st/paisley' => 'Paisley',
  'st/prestwick' => 'Prestwick',
  'st/rothesay' => 'Rothesay',
  'st/tarbert' => 'Tarbert',
  'st/tiree' => 'Tiree',
  'st/wemyss_bay' => 'Wemyss Bay',
  'ta/aberfeldy' => 'Aberfeldy',
  'ta/alloa' => 'Alloa',
  'ta/arbroath' => 'Arbroath',
  'ta/burntisland' => 'Burntisland',
  'ta/dunblane' => 'Dunblane',
  'ta/dundee' => 'Dundee',
  'ta/dunfermline' => 'Dunfermline',
  'ta/falkirk' => 'Falkirk',
  'ta/finnart' => 'Finnart',
  'ta/glenrothes' => 'Glenrothes',
  'ta/kirkcaldy' => 'Kirkcaldy',
  'ta/montrose' => 'Montrose',
  'ta/perth' => 'Perth',
  'ta/rosyth' => 'Rosyth',
  'ta/st_andrews' => 'St Andrews',
  'ta/stirling' => 'Stirling',
  'dg/coldstream' => 'Coldstream',
  'dg/dumfries' => 'Dumfries',
  'dg/dunbar' => 'Dunbar',
  'dg/edinburgh' => 'Edinburgh',
  'dg/eyemouth' => 'Eyemouth',
  'dg/galashiels' => 'Galashiels',
  'dg/hawick' => 'Hawick',
  'dg/kelso' => 'Kelso',
  'dg/leith' => 'Leith',
  'dg/livingston' => 'Livingston',
  'dg/lockerbie' => 'Lockerbie',
  'dg/moffat' => 'Moffat',
  'dg/newton_stewart' => 'Newton Stewart',
  'dg/north_berwick' => 'North Berwick',
  'dg/peebles' => 'Peebles',
  'dg/sanquhar' => 'Sanquhar',
  'dg/stranraer' => 'Stranraer',
  'ni/aldergrove' => 'Aldergrove',
  'ni/armagh' => 'Armagh',
  'ni/ballycastle' => 'Ballycastle',
  'ni/bangor' => 'Bangor',
  'ni/belfast' => 'Belfast',
  'ni/coleraine' => 'Coleraine',
  'ni/enniskillen' => 'Enniskillen',
  'ni/larne' => 'Larne',
  'ni/londonderry' => 'Londonderry',
  'ni/omagh' => 'Omagh',
  'ni/warrenpoint' => 'Warrenpoint',
  'wl/aberaeron' => 'Aberaeron',
  'wl/aberporth' => 'Aberporth',
  'wl/abersoch' => 'Abersoch',
  'wl/aberystwyth' => 'Aberystwyth',
  'wl/barry' => 'Barry',
  'wl/betws-y-coed' => 'Betws-y-coed',
  'wl/brecon' => 'Brecon',
  'wl/builth_wells' => 'Builth Wells',
  'wl/cardiff' => 'Cardiff',
  'wl/carmarthen' => 'Carmarthen',
  'wl/colwyn_bay' => 'Colwyn Bay',
  'wl/fishguard' => 'Fishguard',
  'wl/holyhead' => 'Holyhead',
  'wl/llanddulas' => 'Llanddulas',
  'wl/machynlleth' => 'Machynlleth',
  'wl/merthyr_tydfil' => 'Merthyr Tydfil',
  'wl/milford_haven' => 'Milford Haven',
  'wl/mostyn' => 'Mostyn',
  'wl/neath_abbey' => 'Neath Abbey',
  'wl/newport' => 'Newport',
  'wl/newtown' => 'Newtown',
  'wl/pembroke' => 'Pembroke',
  'wl/port_talbot' => 'Port Talbot',
  'wl/porthmadog' => 'Porthmadog',
  'wl/rhyl' => 'Rhyl',
  'wl/swansea' => 'Swansea',
  'wl/tenby' => 'Tenby',
  'wl/trawscoed' => 'Trawscoed',
  'wl/valley' => 'Valley',
  'wl/welshpool' => 'Welshpool',
  'wl/wrexham' => 'Wrexham',
  'nw/barrow_in_furness' => 'Barrow In Furness',
  'nw/blackburn' => 'Blackburn',
  'nw/blackpool' => 'Blackpool',
  'nw/carlisle' => 'Carlisle',
  'nw/chester' => 'Chester',
  'nw/crewe' => 'Crewe',
  'nw/ellesmere_port' => 'Ellesmere Port',
  'nw/fleetwood' => 'Fleetwood',
  'nw/haslingden' => 'Haslingden',
  'nw/heysham' => 'Heysham',
  'nw/kendal' => 'Kendal',
  'nw/keswick' => 'Keswick',
  'nw/lancaster' => 'Lancaster',
  'nw/liverpool' => 'Liverpool',
  'nw/macclesfield' => 'Macclesfield',
  'nw/manchester' => 'Manchester',
  'nw/penrith' => 'Penrith',
  'nw/preston' => 'Preston',
  'nw/rochdale' => 'Rochdale',
  'nw/runcorn' => 'Runcorn',
  'nw/sedbergh' => 'Sedbergh',
  'nw/silloth' => 'Silloth',
  'nw/warrington' => 'Warrington',
  'nw/whitehaven' => 'Whitehaven',
  'nw/wigan' => 'Wigan',
  'nw/workington' => 'Workington',
  'ne/alnwick' => 'Alnwick',
  'ne/berwick_upon_tweed' => 'Berwick Upon Tweed',
  'ne/bishop_auckland' => 'Bishop Auckland',
  'ne/blyth' => 'Blyth',
  'ne/boulmer' => 'Boulmer',
  'ne/durham' => 'Durham',
  'ne/hartlepool' => 'Hartlepool',
  'ne/middlesbrough' => 'Middlesbrough',
  'ne/newcastle' => 'Newcastle',
  'ne/seaham' => 'Seaham',
  'ne/shotton' => 'Shotton',
  'ne/sunderland' => 'Sunderland',
  'ne/teesport' => 'Teesport',
  'ne/tynemouth' => 'Tynemouth',
  'yh/barnsley' => 'Barnsley',
  'yh/bradford' => 'Bradford',
  'yh/bridlington' => 'Bridlington',
  'yh/doncaster' => 'Doncaster',
  'yh/easington' => 'Easington',
  'yh/goole' => 'Goole',
  'yh/grimsby' => 'Grimsby',
  'yh/halifax' => 'Halifax',
  'yh/harrogate' => 'Harrogate',
  'yh/hetton' => 'Hetton',
  'yh/huddersfield' => 'Huddersfield',
  'yh/hull' => 'Hull',
  'yh/immingham' => 'Immingham',
  'yh/leeds' => 'Leeds',
  'yh/leeming' => 'Leeming',
  'yh/new_holland' => 'New Holland',
  'yh/pontefract' => 'Pontefract',
  'yh/rotherham' => 'Rotherham',
  'yh/scarborough' => 'Scarborough',
  'yh/scunthorpe' => 'Scunthorpe',
  'yh/sheffield' => 'Sheffield',
  'yh/skipton' => 'Skipton',
  'yh/thirsk' => 'Thirsk',
  'yh/wakefield' => 'Wakefield',
  'yh/whitby' => 'Whitby',
  'yh/york' => 'York',
  'wm/alton_towers' => 'Alton Towers',
  'wm/birmingham' => 'Birmingham',
  'wm/bridgnorth' => 'Bridgnorth',
  'wm/bromsgrove' => 'Bromsgrove',
  'wm/burton_on_trent' => 'Burton on Trent',
  'wm/cannock' => 'Cannock',
  'wm/cheadle' => 'Cheadle',
  'wm/church_lawford' => 'Church Lawford',
  'wm/church_stretton' => 'Church Stretton',
  'wm/coventry' => 'Coventry',
  'wm/great_malvern' => 'Great Malvern',
  'wm/hereford' => 'Hereford',
  'wm/ludlow' => 'Ludlow',
  'wm/nuneaton' => 'Nuneaton',
  'wm/pershore' => 'Pershore',
  'wm/ross_on_wye' => 'Ross on Wye',
  'wm/rugby' => 'Rugby',
  'wm/shawbury' => 'Shawbury',
  'wm/shrewsbury' => 'Shrewsbury',
  'wm/solihull' => 'Solihull',
  'wm/stafford' => 'Stafford',
  'wm/stoke' => 'Stoke',
  'wm/stratford-upon-avon' => 'Stratford-upon-Avon',
  'wm/sutton_coldfield' => 'Sutton Coldfield',
  'wm/telford' => 'Telford',
  'wm/uttoxeter' => 'Uttoxeter',
  'wm/walsall' => 'Walsall',
  'wm/warwick' => 'Warwick',
  'wm/west_bromwich' => 'West Bromwich',
  'wm/wolverhampton' => 'Wolverhampton',
  'wm/worcester' => 'Worcester',
  'em/alfreton' => 'Alfreton',
  'em/bakewell' => 'Bakewell',
  'em/boston' => 'Boston',
  'em/bottesford' => 'Bottesford',
  'em/buxton' => 'Buxton',
  'em/castle_donington' => 'Castle Donington',
  'em/chesterfield' => 'Chesterfield',
  'em/corby' => 'Corby',
  'em/daventry' => 'Daventry',
  'em/derby' => 'Derby',
  'em/gainsborough' => 'Gainsborough',
  'em/hinckley' => 'Hinckley',
  'em/kettering' => 'Kettering',
  'em/killingholme' => 'Killingholme',
  'em/leicester' => 'Leicester',
  'em/lincoln' => 'Lincoln',
  'em/mansfield' => 'Mansfield',
  'em/market_harborough' => 'Market Harborough',
  'em/melton_mowbray' => 'Melton Mowbray',
  'em/newark' => 'Newark',
  'em/northampton' => 'Northampton',
  'em/nottingham' => 'Nottingham',
  'em/oakham' => 'Oakham',
  'em/rampton' => 'Rampton',
  'em/retford' => 'Retford',
  'em/skegness' => 'Skegness',
  'em/spalding' => 'Spalding',
  'em/sudbury' => 'Sudbury',
  'em/towcester' => 'Towcester',
  'em/wellingborough' => 'Wellingborough',
  'em/worksop' => 'Worksop',
  'ee/ampthill' => 'Ampthill',
  'ee/basildon' => 'Basildon',
  'ee/bedford' => 'Bedford',
  'ee/biggleswade' => 'Biggleswade',
  'ee/brightlingsea' => 'Brightlingsea',
  'ee/bury_st_edmunds' => 'Bury St Edmunds',
  'ee/cambridge' => 'Cambridge',
  'ee/chelmsford' => 'Chelmsford',
  'ee/colchester' => 'Colchester',
  'ee/cranfield' => 'Cranfield',
  'ee/docking' => 'Docking',
  'ee/fakenham' => 'Fakenham',
  'ee/felixstowe' => 'Felixstowe',
  'ee/garston' => 'Garston',
  'ee/great_yarmouth' => 'Great Yarmouth',
  'ee/harlow' => 'Harlow',
  'ee/harwich' => 'Harwich',
  'ee/hemel_hempstead' => 'Hemel Hempstead',
  'ee/hertford' => 'Hertford',
  'ee/huntingdon' => 'Huntingdon',
  'ee/ipswich' => 'Ipswich',
  'ee/kings_lynn' => 'King\'s Lynn',
  'ee/letchworth' => 'Letchworth',
  'ee/lowestoft' => 'Lowestoft',
  'ee/luton' => 'Luton',
  'ee/maldon' => 'Maldon',
  'ee/mistley' => 'Mistley',
  'ee/norwich' => 'Norwich',
  'ee/peterborough' => 'Peterborough',
  'ee/ramsey' => 'Ramsey',
  'ee/shoeburyness' => 'Shoeburyness',
  'ee/southend-on-sea' => 'Southend-on-Sea',
  'ee/southwold' => 'Southwold',
  'ee/st_albans' => 'St Albans',
  'ee/stansted' => 'Stansted',
  'ee/thetford' => 'Thetford',
  'ee/tilbury' => 'Tilbury',
  'ee/wattisham' => 'Wattisham',
  'ee/wisbech' => 'Wisbech',
  'ee/wittering' => 'Wittering',
  'ee/woburn' => 'Woburn',
  'sw/appledore' => 'Appledore',
  'sw/avonmouth' => 'Avonmouth',
  'sw/barnstaple' => 'Barnstaple',
  'sw/bath' => 'Bath',
  'sw/bideford' => 'Bideford',
  'sw/bodmin' => 'Bodmin',
  'sw/boscombe_down' => 'Boscombe Down',
  'sw/bournemouth' => 'Bournemouth',
  'sw/bridport' => 'Bridport',
  'sw/bristol' => 'Bristol',
  'sw/brixham' => 'Brixham',
  'sw/bude' => 'Bude',
  'sw/camborne' => 'Camborne',
  'sw/cheltenham' => 'Cheltenham',
  'sw/cirencester' => 'Cirencester',
  'sw/dartmouth' => 'Dartmouth',
  'sw/devizes' => 'Devizes',
  'sw/dorchester' => 'Dorchester',
  'sw/exeter' => 'Exeter',
  'sw/exmouth' => 'Exmouth',
  'sw/falmouth' => 'Falmouth',
  'sw/fowey' => 'Fowey',
  'sw/gloucester' => 'Gloucester',
  'sw/ilfracombe' => 'Ilfracombe',
  'sw/marlborough' => 'Marlborough',
  'sw/newquay' => 'Newquay',
  'sw/padstow' => 'Padstow',
  'sw/par' => 'Par',
  'sw/penzance' => 'Penzance',
  'sw/plymouth' => 'Plymouth',
  'sw/poole' => 'Poole',
  'sw/portland' => 'Portland',
  'sw/salcombe' => 'Salcombe',
  'sw/salisbury' => 'Salisbury',
  'sw/sharpness' => 'Sharpness',
  'sw/st_ives' => 'St Ives',
  'sw/st_marys' => 'St Mary\'s',
  'sw/stonehouse' => 'Stonehouse',
  'sw/swindon' => 'Swindon',
  'sw/taunton' => 'Taunton',
  'sw/tavistock' => 'Tavistock',
  'sw/teignmouth' => 'Teignmouth',
  'sw/tewkesbury' => 'Tewkesbury',
  'sw/the_lizard' => 'The Lizard',
  'sw/torquay' => 'Torquay',
  'sw/truro' => 'Truro',
  'sw/warminster' => 'Warminster',
  'sw/watchet' => 'Watchet',
  'sw/wells' => 'Wells',
  'sw/weymouth' => 'Weymouth',
  'sw/yeovilton' => 'Yeovilton',
  'se/abingdon' => 'Abingdon',
  'se/aldershot' => 'Aldershot',
  'se/amersham' => 'Amersham',
  'se/andover' => 'Andover',
  'se/ashford' => 'Ashford',
  'se/aylesbury' => 'Aylesbury',
  'se/banbury' => 'Banbury',
  'se/barking' => 'Barking',
  'se/barnet' => 'Barnet',
  'se/basingstoke' => 'Basingstoke',
  'se/battle' => 'Battle',
  'se/benson' => 'Benson',
  'se/biggin_hill' => 'Biggin Hill',
  'se/bognor_regis' => 'Bognor Regis',
  'se/bracknell' => 'Bracknell',
  'se/brighton' => 'Brighton',
  'se/brize_norton' => 'Brize Norton',
  'se/buckingham' => 'Buckingham',
  'se/canterbury' => 'Canterbury',
  'se/chatham' => 'Chatham',
  'se/cowes' => 'Cowes',
  'se/crawley' => 'Crawley',
  'se/croydon' => 'Croydon',
  'se/dorking' => 'Dorking',
  'se/dover' => 'Dover',
  'se/eastbourne' => 'Eastbourne',
  'se/farnham' => 'Farnham',
  'se/folkestone' => 'Folkestone',
  'se/gatwick' => 'Gatwick',
  'se/gravesend' => 'Gravesend',
  'se/guildford' => 'Guildford',
  'se/hastings' => 'Hastings',
  'se/heathrow' => 'Heathrow',
  'se/henley_on_thames' => 'Henley on Thames',
  'se/herstmonceux' => 'Herstmonceux',
  'se/high_wycombe' => 'High Wycombe',
  'se/horsham' => 'Horsham',
  'se/hove' => 'Hove',
  'se/hungerford' => 'Hungerford',
  'se/kingston_upon_thames' => 'Kingston upon Thames',
  'se/leatherhead' => 'Leatherhead',
  'se/littlehampton' => 'Littlehampton',
  'se/london' => 'London',
  'se/lydd' => 'Lydd',
  'se/maidenhead' => 'Maidenhead',
  'se/maidstone' => 'Maidstone',
  'se/manston' => 'Manston',
  'se/margate' => 'Margate',
  'se/newbury' => 'Newbury',
  'se/newhaven' => 'Newhaven',
  'se/northolt' => 'Northolt',
  'se/oxford' => 'Oxford',
  'se/portsmouth' => 'Portsmouth',
  'se/ramsgate' => 'Ramsgate',
  'se/reading' => 'Reading',
  'se/reigate' => 'Reigate',
  'se/rye' => 'Rye',
  'se/selsey' => 'Selsey',
  'se/sheerness' => 'Sheerness',
  'se/shoreham-by-sea' => 'Shoreham-by-Sea',
  'se/southampton' => 'Southampton',
  'se/st_catherines_point' => 'St Catherine\'s Point',
  'se/tunbridge_wells' => 'Tunbridge Wells',
  'se/uckfield' => 'Uckfield',
  'se/whitstable' => 'Whitstable',
  'se/windsor' => 'Windsor',
  'se/winslow' => 'Winslow',
  'se/woking_' => 'Woking ',
);

?>
