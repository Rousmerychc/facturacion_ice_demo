const Dsig = require('pkcs12-xml');
var dsig = new Dsig('softoken.p12);

try{
	dsig.openSession('AdscSistemas1');
} catch (e){
	console.error(e);
} finally {
	dsig.closeSession();
}
var xml = '<libro><titulo>Viaje al centro de la tierra</titulo><autor>Julio Berne</autor></libro>';
    console.log(dsig.computeSignature(xml, 'titulo'));

