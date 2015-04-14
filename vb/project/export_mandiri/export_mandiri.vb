Imports System.Data.SqlClient
Imports System.IO
Imports System.Globalization
Imports Microsoft.Office.Interop

Module export_mandiri

    Sub Main()

        Dim app_path,
            conn_txt, status_txt, respon_txt,
            msg_respon As String

        msg_respon = ""

        app_path = My.Application.Info.DirectoryPath
        'app_path = "F:\UwAmp\www\pkb\vb\export\mandiri"

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
        Dim periode As String = time.ToString("yyyyMM")
        Dim i As Integer = 1

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
                .Cells(i, 1).Value = "key1"
                .Cells(i, 2).Value = "key2"
                .Cells(i, 3).Value = "key3"
                .Cells(i, 4).Value = "currency"
                .Cells(i, 5).Value = "bill_info_01"
                .Cells(i, 6).Value = "bill_info_02"
                .Cells(i, 7).Value = "bill_info_03"
                .Cells(i, 8).Value = "bill_info_04"
                .Cells(i, 9).Value = "bill_info_05"
                .Cells(i, 10).Value = "bill_info_06"
                .Cells(i, 11).Value = "bill_info_07"
                .Cells(i, 12).Value = "bill_info_08"
                .Cells(i, 13).Value = "bill_info_09"
                .Cells(i, 14).Value = "bill_info_10"
                .Cells(i, 15).Value = "bill_info_11"
                .Cells(i, 16).Value = "bill_info_12"
                .Cells(i, 17).Value = "bill_info_13"
                .Cells(i, 18).Value = "bill_info_14"
                .Cells(i, 19).Value = "bill_info_15"
                .Cells(i, 20).Value = "bill_info_16"
                .Cells(i, 21).Value = "bill_info_17"
                .Cells(i, 22).Value = "bill_info_18"
                .Cells(i, 23).Value = "bill_info_19"
                .Cells(i, 24).Value = "bill_info_20"
                .Cells(i, 25).Value = "bill_info_21"
                .Cells(i, 26).Value = "bill_info_22"
                .Cells(i, 27).Value = "bill_info_23"
                .Cells(i, 28).Value = "bill_info_24"
                .Cells(i, 29).Value = "bill_info_25"
                .Cells(i, 30).Value = "periode_open"
                .Cells(i, 31).Value = "periode_close"
                .Cells(i, 32).Value = "subbill_01"
                .Cells(i, 33).Value = "subbill_02"
                .Cells(i, 34).Value = "subbill_03"
                .Cells(i, 35).Value = "subbill_04"
                .Cells(i, 36).Value = "subbill_05"
                .Cells(i, 37).Value = "subbill_06"
                .Cells(i, 38).Value = "subbill_07"
                .Cells(i, 39).Value = "subbill_08"
                .Cells(i, 40).Value = "subbill_09"
                .Cells(i, 41).Value = "subbill_10"
                .Cells(i, 42).Value = "subbill_11"
                .Cells(i, 43).Value = "subbill_12"
                .Cells(i, 44).Value = "subbill_13"
                .Cells(i, 45).Value = "subbill_14"
                .Cells(i, 46).Value = "subbill_15"
                .Cells(i, 47).Value = "subbill_16"
                .Cells(i, 48).Value = "subbill_17"
                .Cells(i, 49).Value = "subbill_18"
                .Cells(i, 50).Value = "subbill_19"
                .Cells(i, 51).Value = "subbill_20"
                .Cells(i, 52).Value = "subbill_21"
                .Cells(i, 53).Value = "subbill_22"
                .Cells(i, 54).Value = "subbill_23"
                .Cells(i, 55).Value = "subbill_24"
                .Cells(i, 56).Value = "subbill_25"
                .Cells(i, 57).Value = "end_record"

            End With

            ' ============== DB ==============

            Dim conn_str = File.ReadAllText(conn_txt)
            conn = New SqlConnection(conn_str)
            cmd.Connection = conn
            conn.Open()

            cmd.Connection = conn
            cmd.CommandText = "" +
            "SELECT " +
                "b.NO_PELANGGAN, " +
                "(SELECT (KODE_BLOK + '|' + NAMA_PELANGGAN) FROM KWT_PELANGGAN WHERE NO_PELANGGAN = b.NO_PELANGGAN) AS BLOK_NAPEL, " +
                "ISNULL(SUM(ISNULL(b.BLOK1,0) + ISNULL(b.BLOK2,0) + ISNULL(b.BLOK3,0) + ISNULL(b.BLOK4,0)),0) AS PEMAKAIAN , " +
                "SUM(ISNULL(b.ABONEMEN,0)) AS ABONEMEN, " +
                "SUM(ISNULL(b.JUMLAH_AIR,0) - ISNULL(b.DISKON_RUPIAH_AIR,0)) AS JUMLAH_AIR, " +
                "SUM(ISNULL(b.JUMLAH_IPL,0) - ISNULL(b.DISKON_RUPIAH_IPL,0)) AS JUMLAH_IPL, " +
                "SUM(ISNULL(b.DENDA,0)) AS DENDA " +
            "FROM " +
                "KWT_PEMBAYARAN_AI b " +
            "WHERE " +
                "b.STATUS_BAYAR IS NULL " +
            "GROUP BY b.NO_PELANGGAN " +
            "ORDER BY BLOK_NAPEL "

            row = cmd.ExecuteReader()

            ' ============== CHECK FILE ==============

            Dim save_file_xls As String = app_path + "\files\MANDIRI_EXPORT_" + periode + ".xls"

            ' ============== FILL DATA ==============

            Dim bill_info_03 As String = time.ToString("MM-yyyy")
            Dim range_time As DateTime = DateTime.ParseExact(periode + "01", "yyyyMMdd", CultureInfo.InvariantCulture)
            Dim periode_open As String = range_time.AddMonths(1).ToString("yyyyMMdd")
            Dim periode_close As String = range_time.AddMonths(2).AddDays(-1).ToString("yyyyMMdd")

            Dim v(56), SPLIT_BLOK_NAPEL() As String

            Do While row.Read()

                i = i + 1

                SPLIT_BLOK_NAPEL = row("BLOK_NAPEL").Split("|")

                v(0) = "'" + row("NO_PELANGGAN").ToString
                v(3) = "IDR"
                v(4) = SPLIT_BLOK_NAPEL(1)
                v(5) = SPLIT_BLOK_NAPEL(0)
                v(6) = bill_info_03
                v(7) = row("PEMAKAIAN").ToString
                v(29) = periode_open
                v(30) = periode_close
                v(31) = "01\Air Bersih\Air\" + row("JUMLAH_AIR").ToString
                v(32) = "02\IPL\IPL\" + row("JUMLAH_IPL").ToString
                v(33) = "03\Abonemen\Abonemen\" + row("ABONEMEN").ToString
                v(34) = "04\Denda\Denda\" + row("DENDA").ToString

                ws.Range("A" + i.ToString, "BE" + i.ToString).Value = v

            Loop

            row.Close()


            xls.Visible = True
            xls.UserControl = True
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
