Imports System.Data.SqlClient
Imports System.IO
Imports Microsoft.Office.Interop
Imports System.Globalization

Module export_bumiputera

    Sub Main()

        Dim app_path,
            conn_txt, status_txt, respon_txt,
            msg_respon As String

        msg_respon = ""

        app_path = My.Application.Info.DirectoryPath
        'app_path = "F:\UwAmp\www\pkb\vb\export\bumiputera"

        conn_txt = Path.GetDirectoryName(Path.GetDirectoryName(app_path)) + "\conn.txt"
        status_txt = app_path + "\status.txt"
        respon_txt = app_path + "\respon.txt"

        If File.Exists(status_txt) = False Then
            File.AppendAllText(status_txt, "")
        End If
        If File.Exists(respon_txt) = False Then
            File.AppendAllText(respon_txt, "")
        End If
        If File.Exists(conn_txt) = False Then
            File.WriteAllText(status_txt, "FINISH")
            File.WriteAllText(respon_txt, "File conn.txt tidak ditemukan! hubungi MSI !")
            Environment.Exit(0)
        End If

        ' ============================= SET ACTION =============================

        Dim conn As New SqlConnection
        Dim cmd As New SqlCommand
        Dim row As SqlDataReader

        Dim xls As Excel.Application
        Dim wb As Excel.Workbook
        Dim ws As Excel.Worksheet

        Dim time As DateTime = Date.Now()
        Dim periode_tag As String = time.ToString("yyyyMM")
        Dim i As Integer = 1
        Dim n As Integer = 1

        ' ============================= HERE WE GO =============================

        Try

            ' ============== STATUS & RESULT ==============

            File.WriteAllText(status_txt, "PROSES")
            File.WriteAllText(respon_txt, "")

            ' ============== EXCEL ==============

            xls = CreateObject("Excel.Application")
            wb = xls.Workbooks.Add()
            ws = wb.ActiveSheet

            With ws
                .Cells(i, 1).Value = "MASTER PAM JRP"

                i = i + 1
                .Cells(i, 1).Value = "AGUSTUS 2014 "

                i = i + 1
                .Cells(i, 1).Value = "No"
                .Cells(i, 2).Value = "Kode Blok"
                .Cells(i, 3).Value = "Nama Pelanggan"
                .Cells(i, 4).Value = "Tagihan JRP"
            End With

            ' ============== DB ==============

            Dim conn_str = File.ReadAllText(conn_txt)
            conn = New SqlConnection(conn_str)
            cmd.Connection = conn
            conn.Open()

            cmd.Connection = conn
            cmd.CommandText = "" +
            "SELECT " +
                "ISNULL((SELECT KODE_BLOK FROM KWT_PELANGGAN WHERE NO_PELANGGAN = b.NO_PELANGGAN), '#NULL#'+b.NO_PELANGGAN) AS KODE_BLOK, " +
                "ISNULL((SELECT NAMA_PELANGGAN FROM KWT_PELANGGAN WHERE NO_PELANGGAN = b.NO_PELANGGAN), '#NULL#'+b.NO_PELANGGAN) AS NAMA_PELANGGAN, " +
                "SUM(ISNULL(b.JUMLAH_AIR,0) + ISNULL(b.ABONEMEN,0) + ISNULL(b.JUMLAH_IPL,0) + ISNULL(b.DENDA,0) - ISNULL(b.DISKON_AIR,0) - ISNULL(b.DISKON_IPL,0)) AS TOTAL " +
            "FROM " +
                "KWT_PEMBAYARAN_AI b " +
            "WHERE " +
                "b.STATUS_BAYAR = 0 " +
            "GROUP BY b.NO_PELANGGAN " +
            "ORDER BY b.NO_PELANGGAN "

            row = cmd.ExecuteReader()

            ' ============== CHECK FILE ==============

            Dim save_file_xls As String = app_path + "\files\BUMIPUTERA_EXPORT_" + periode_tag + ".xls"

            ' ============== FILL DATA ==============

            Dim v(3) As String

            Do While row.Read()

                i = i + 1

                v(0) = n
                v(1) = row("KODE_BLOK")
                v(2) = row("NAMA_PELANGGAN")
                v(3) = row("TOTAL")

                ws.Range("A" + i.ToString, "D" + i.ToString).Value = v

                n = n + 1
            Loop

            row.Close()

            xls.Visible = True
            xls.UserControl = True
            File.Delete(save_file_xls)
            ws.SaveAs(save_file_xls)
            wb.Close()

            wb = Nothing
            ws = Nothing
            xls.Quit()
            xls = Nothing

        Catch ex As NullReferenceException

            ' SKIP FOR THIS ERROR

        Catch ex As Exception

            msg_respon = ex.Message + vbNewLine

        Finally

            conn.Close()

            File.WriteAllText(status_txt, "FINISH")
            File.WriteAllText(respon_txt, msg_respon)

            'Console.ReadLine()
            Environment.Exit(0)

        End Try

    End Sub

End Module
