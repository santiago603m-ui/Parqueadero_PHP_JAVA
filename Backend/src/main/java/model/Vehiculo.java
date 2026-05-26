package model;

public class Vehiculo {
    private int id;
    private String placa;
    private String tipo;
    private String propietario;
    private String telefono;

    public Vehiculo() {
    }

    public Vehiculo(int id, String placa, String tipo, String propietario, String telefono) {
        this.id = id;
        this.placa = placa;
        this.tipo = tipo;
        this.propietario = propietario;
        this.telefono = telefono;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getPlaca() {
        return placa;
    }

    public void setPlaca(String placa) {
        this.placa = placa;
    }

    public String getTipo() {
        return tipo;
    }

    public void setTipo(String tipo) {
        this.tipo = tipo;
    }

    public String getPropietario() {
        return propietario;
    }

    public void setPropietario(String propietario) {
        this.propietario = propietario;
    }

    public String getTelefono() {
        return telefono;
    }

    public void setTelefono(String telefono) {
        this.telefono = telefono;
    }

    public String toJson() {
        return String.format(
                "{\"id\":%d,\"placa\":\"%s\",\"tipo\":\"%s\",\"propietario\":\"%s\",\"telefono\":\"%s\"}",
                id, placa, tipo, propietario, telefono);
    }
}