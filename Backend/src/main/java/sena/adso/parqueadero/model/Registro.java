package sena.adso.parqueadero.model;

import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;
import java.util.Locale;

/**
 * Modelo Registro - Entrada/Salida de vehículos
 * Parqueadero Boyacá - SENA CIMM ADSO
 */
public class Registro {
    private int id;
    private int vehiculoId;
    private String placa;           // join con vehiculo
    private String tipo;            // join con vehiculo
    private LocalDateTime entrada;
    private LocalDateTime salida;   // null si aún está adentro
    private double tarifa;
    private String estado;          // ACTIVO, FINALIZADO

    private static final DateTimeFormatter FMT = DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm:ss");

    public Registro() {}

    // Getters y Setters
    public int getId() { return id; }
    public void setId(int id) { this.id = id; }

    public int getVehiculoId() { return vehiculoId; }
    public void setVehiculoId(int vehiculoId) { this.vehiculoId = vehiculoId; }

    public String getPlaca() { return placa; }
    public void setPlaca(String placa) { this.placa = placa; }

    public String getTipo() { return tipo; }
    public void setTipo(String tipo) { this.tipo = tipo; }

    public LocalDateTime getEntrada() { return entrada; }
    public void setEntrada(LocalDateTime entrada) { this.entrada = entrada; }

    public LocalDateTime getSalida() { return salida; }
    public void setSalida(LocalDateTime salida) { this.salida = salida; }

    public double getTarifa() { return tarifa; }
    public void setTarifa(double tarifa) { this.tarifa = tarifa; }

    public String getEstado() { return estado; }
    public void setEstado(String estado) { this.estado = estado; }

    public String toJson() {
        String salidaStr = (salida != null) ? "\"" + salida.format(FMT) + "\"" : "null";
        return String.format(Locale.US,
            "{\"id\":%d,\"vehiculoId\":%d,\"placa\":\"%s\",\"tipo\":\"%s\"," +
            "\"entrada\":\"%s\",\"salida\":%s,\"tarifa\":%.2f,\"estado\":\"%s\"}",
            id, vehiculoId, placa, tipo,
            entrada.format(FMT), salidaStr, tarifa, estado
        );
    }
}
