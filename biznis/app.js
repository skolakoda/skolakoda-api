angular
  .module('adminApp', [])
  .controller('biznisKontroler', function biznisKontroler() {

    this.brojPredavaca = 3
    this.polaznika = 15
    this.clanarina = 3000
    this.cenaProstora = 25000

    this.bruto = () => this.polaznika * this.clanarina
    this.neto = () => this.bruto() - this.cenaProstora
    this.zarada = () => (this.neto() / this.brojPredavaca).toFixed(2)

  })
