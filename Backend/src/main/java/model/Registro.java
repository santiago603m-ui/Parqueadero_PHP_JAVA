package model;

import java.sql.Timestamp;

public class Registro {
    private int id;
    private int vehiculoId;
    private Timestamp entrada;
    private Timestamp salida;
    private double tarifa;
    private String estado;

    public Registro() {
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public int getVehiculoId() {
        return vehiculoId;
    }

    public void setVehiculoId(int vehiculoId) {
        this.vehiculoId = vehiculoId;
    }

    public Timestamp getEntrada() {
        return entrada;
    }

    public void setEntrada(Timestamp entrada) {
        this.entrada = entrada;
    }

    public Timestamp getSalida() {
        return salida;
    }

    public void setSalida(Timestamp salida) {
        this.salida = salida;
    }

    public double getTarifa() {
        return tarifa;
    }

    public void setTarifa(double tarifa) {
        this.tarifa = tarifa;
    }

    public String getEstado() {
        return estado;
    }

    public void setEstado(String estado) {
        this.estado = estado;
    }

    public String toJson() {
        String salidaStr = (salida == null) ? "null" : "\"" + salida.toString() + "\"";
        return String.format(
                "{\"id\":%d,\"vehiculoId\":%d,\"entrada\":\"%s\",\"salida\":%s,\"tarifa\":%.2f,\"estado\":\"%s\"}",
                id, vehiculoId, entrada.toString(), salidaStr, tarifa, estado);
    }
}