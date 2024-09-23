import pyodbc
from tabulate import tabulate
import tkinter as tk
from tkinter import messagebox, Scrollbar, Frame, Label, Canvas

# Configura la cadena de conexión
server = 'DESKTOP-QB22C4J\SQLEXPRESS'
database = 'Distribuidora'
username = 'sa'
password = '123'

def mostrar_mensaje():
    messagebox.showinfo("Mensaje", "EL APLAUSO SEÑOREEEEES")

# Crear la conexión
conn_str = f'DRIVER={{ODBC Driver 17 for SQL Server}};SERVER={server};DATABASE={database};UID={username};PWD={password}'
try:
    # Conectar a la base de datos
    conn = pyodbc.connect(conn_str)
    print("Conexión exitosa a la base de datos.")
    
    cursor = conn.cursor()
    
    # Consulta para obtener las tablas
    query_tables = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'"
    cursor.execute(query_tables)

    # Recoger los nombres de las tablas
    tables = cursor.fetchall()

    # Crear la ventana principal
    ventana = tk.Tk()
    ventana.title("Tablas de la Base de Datos")

    # Crear un frame para el scroll
    scroll_frame = Frame(ventana)
    scroll_frame.pack(fill=tk.BOTH, expand=True)

    canvas = Canvas(scroll_frame)
    scrollbar = Scrollbar(scroll_frame, orient="vertical", command=canvas.yview)
    scrollable_frame = Frame(canvas)

    scrollable_frame.bind(
        "<Configure>",
        lambda e: canvas.configure(scrollregion=canvas.bbox("all"))
    )

    canvas.create_window((0, 0), window=scrollable_frame, anchor="nw")

    canvas.pack(side=tk.LEFT, fill=tk.BOTH, expand=True)
    scrollbar.pack(side=tk.RIGHT, fill=tk.Y)

    canvas.configure(yscrollcommand=scrollbar.set)

    # Función para manejar el desplazamiento con la rueda del mouse
    def on_mouse_wheel(event):
        canvas.yview_scroll(int(-1*(event.delta/120)), "units")

    # Vincular el evento de la rueda del mouse al Canvas
    ventana.bind_all("<MouseWheel>", on_mouse_wheel)  # Para Windows
    # Para Linux, usa las siguientes dos líneas en lugar de la línea anterior:
    # ventana.bind_all("<Button-4>", lambda e: on_mouse_wheel(e, 1))  # Desplazar hacia arriba
    # ventana.bind_all("<Button-5>", lambda e: on_mouse_wheel(e, -1))  # Desplazar hacia abajo

    if tables:
        # Iterar sobre las tablas
        for filas in tables:
            table_name = filas[0]
            print(f"\nAtributos de la tabla: {table_name}")

            # Consulta para obtener los atributos (columnas) de la tabla
            query_columns = f"""SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '{table_name}'"""
            cursor.execute(query_columns)

            # Recoger los atributos
            columns = cursor.fetchall()

            # Mostrar los atributos en formato de tabla
            if columns:
                tabla_formateada = tabulate(columns, headers=["Nombre de Columna", "Tipo de Dato"], tablefmt="grid")
                print(tabla_formateada)

                # Agregar un cuadro de texto en la ventana
                label = Label(scrollable_frame, text=f"Atributos de la tabla: {table_name}\n{tabla_formateada}", justify=tk.LEFT)
                label.pack(pady=10)
            else:
                print("No se encontraron columnas en esta tabla.")
                label = Label(scrollable_frame, text=f"Atributos de la tabla: {table_name}\nNo se encontraron columnas.", justify=tk.LEFT)
                label.pack(pady=10)

        # Crear un botón
        boton = tk.Button(scrollable_frame, text="HOLA BROOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO", command=mostrar_mensaje)
        boton.pack(pady=20)

        # Ejecutar el bucle principal de la aplicación
        ventana.mainloop()
    else:
        print("No se encontraron tablas en la base de datos.")

    cursor.close()
    conn.close()

except pyodbc.Error as ex:
    print("Error al conectar a la base de datos:", ex)
    input("\npresione ENTER para Cerrar")

# No olvides cerrar la conexión cuando termines
input("\npresione ENTER para Cerrar")
